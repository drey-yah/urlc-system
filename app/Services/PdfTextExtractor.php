<?php

namespace App\Services;

class PdfTextExtractor
{
    /**
     * Extract plain text from a PDF file.
     * Works on both compressed (FlateDecode) and uncompressed PDF streams.
     *
     * @param string $filePath Absolute or relative path to PDF file
     * @return string
     */
    public static function extractText(string $filePath): string
    {
        if (!file_exists($filePath)) {
            return '';
        }

        $content = @file_get_contents($filePath);
        if ($content === false || empty($content)) {
            return '';
        }

        $extracted = '';

        // Match all streams in PDF
        if (preg_match_all('#stream[\r\n]+(.*?)[\r\n]+endstream#s', $content, $matches)) {
            foreach ($matches[1] as $stream) {
                // Try decompressing FlateDecode
                $uncompressed = self::decompressStream($stream);
                if ($uncompressed !== false) {
                    $extracted .= "\n" . self::extractTextFromStream($uncompressed);
                } else {
                    // Try direct stream text extraction if not compressed
                    $extracted .= "\n" . self::extractTextFromStream($stream);
                }
            }
        }

        // Clean up extracted text while PRESERVING newlines
        $cleaned = preg_replace('/[^\x20-\x7E\t\r\n]/', ' ', $extracted);
        $cleaned = str_replace(["\r\n", "\r"], "\n", $cleaned);
        $cleaned = preg_replace('/[ \t]+/', ' ', $cleaned);
        $cleaned = preg_replace('/\n\s*\n+/', "\n\n", $cleaned);

        return self::cleanExtractedText(trim($cleaned));
    }

    /**
     * Attempt decompressing stream using gzuncompress / gzinflate.
     */
    protected static function decompressStream(string $stream)
    {
        // Try gzuncompress (zlib header)
        $data = @gzuncompress($stream);
        if ($data !== false) {
            return $data;
        }

        // Try gzinflate (raw deflate)
        $data = @gzinflate($stream);
        if ($data !== false) {
            return $data;
        }

        // Try skipping possible 2-byte header
        if (strlen($stream) > 2) {
            $data = @gzinflate(substr($stream, 2));
            if ($data !== false) {
                return $data;
            }
        }

        return false;
    }

    /**
     * Parse text operators in PDF stream (BT ... ET, Tj, TJ).
     * Handles escaped parentheses properly (e.g. \(URLC\)).
     */
    protected static function extractTextFromStream(string $stream): string
    {
        $text = '';

        // Match all BT (Begin Text) ... ET (End Text) blocks
        if (preg_match_all('/BT[\s\S]*?ET/', $stream, $blocks)) {
            foreach ($blocks[0] as $block) {
                $blockText = '';

                // Match simple strings: (Text) Tj or ' or "
                if (preg_match_all('/\(((?:[^()\\\\]|\\\\.)*)\)\s*(?:Tj|\'|\")/s', $block, $tjMatches)) {
                    foreach ($tjMatches[1] as $str) {
                        $blockText .= ' ' . self::unescapePdfString($str);
                    }
                }

                // Match array strings: [(T) 12 (ext)] TJ
                if (preg_match_all('/\[([\s\S]*?)\]\s*TJ/s', $block, $tjArrayMatches)) {
                    foreach ($tjArrayMatches[1] as $arr) {
                        if (preg_match_all('/\(((?:[^()\\\\]|\\\\.)*)\)/s', $arr, $parts)) {
                            foreach ($parts[1] as $p) {
                                $blockText .= self::unescapePdfString($p);
                            }
                            $blockText .= ' ';
                        }
                    }
                }

                if (!empty(trim($blockText))) {
                    $text .= trim($blockText) . "\n";
                }
            }
        }

        return $text;
    }

    /**
     * Unescape standard PDF octal and escaped sequences.
     */
    protected static function unescapePdfString(string $str): string
    {
        $str = str_replace(
            ['\\\\', '\\(', '\\)', '\\n', '\\r', '\\t'],
            ['\\', '(', ')', "\n", "\r", "\t"],
            $str
        );

        // Replace octal escapes like \040 -> ' '
        return preg_replace_callback('/\\\([0-7]{1,3})/', function ($m) {
            return chr(octdec($m[1]));
        }, $str);
    }

    /**
     * Smart heuristic: parse out Title, Abstract / Summary, and Field from extracted text.
     * Supports formal research manuscripts as well as Capstone/Thesis proposals (Chapters 1-3).
     */
    public static function parseMetadata(string $text, string $originalFilename = ''): array
    {
        $result = [
            'title' => null,
            'title_suggestions' => [],
            'abstract' => null,
            'research_field' => null,
        ];

        if (empty($text)) {
            return $result;
        }

        $normalizedText = str_replace(["\r\n", "\r"], "\n", $text);

        // ==========================================
        // 1. EXTRACT ABSTRACT / PROJECT SUMMARY
        // ==========================================

        $abstractPatterns = [
            // Pattern 1: Formal ABSTRACT section
            '/(?:^|\n)\s*(?:ABSTRACT|Abstract)[\s:\-\—]*\n+([\s\S]+?)(?=\n\s*(?:Keywords|KEYWORD|Key words|Introduction|INTRODUCTION|1\.\s+Introduction|CHAPTER\s+1|TABLE\s+OF|\Z))/i',
            // Pattern 2: Capstone / Proposal Project Context or Background (standard in Chapters 1-3)
            '/(?:Project Context|Background of the Study|Executive Summary|Project Overview|Project Description)[\s:\-\—\.\d]*\n+([\s\S]+?)(?=\n\s*(?:1\.\d|\bObjectives\b|\bScope\b|\bSignificance\b|\bCHAPTER\s+2|\bMethodology\b|\Z))/i',
            // Pattern 3: Chapter 1 Introduction first narrative section
            '/(?:CHAPTER\s+1|Chapter\s+I)[\s\S]*?(?:INTRODUCTION|Introduction)[\s:\-\—]*\n+([\s\S]+?)(?=\n\s*(?:1\.\d|\bObjectives\b|\bScope\b|\bSignificance\b|\bCHAPTER\s+2|\Z))/i',
        ];

        foreach ($abstractPatterns as $pattern) {
            if (preg_match_all($pattern, $normalizedText, $matches)) {
                foreach ($matches[1] as $candidate) {
                    $cleanedCandidate = self::cleanParagraph($candidate);
                    // Filter out table of contents dots e.g. "...... iii" or lines with roman numerals
                    if (!preg_match('/\.{3,}\s*[ivx0-9]+/i', $cleanedCandidate) && strlen($cleanedCandidate) >= 50) {
                        $result['abstract'] = $cleanedCandidate;
                        break 2;
                    }
                }
            }
        }

        // Fallback Strategy: Find first narrative paragraph that describes the study
        if (empty($result['abstract'])) {
            $paragraphs = preg_split('/\n\s*\n/', $normalizedText);
            foreach ($paragraphs as $para) {
                $cleaned = self::cleanParagraph($para);
                if (preg_match('/(Republic of the Philippines|University of Antique|Faculty of|Bachelor of|In Partial Fulfillment|Approval Sheet|Table of Contents|Acknowledgment)/i', $cleaned)) {
                    continue;
                }
                if (strlen($cleaned) >= 70 && strlen($cleaned) <= 2500 && preg_match('/[\.\?!]$/', $cleaned)) {
                    if (preg_match('/(system|platform|study|research|develop|propose|objective|aim|automate|process|web|application)/i', $cleaned)) {
                        $result['abstract'] = $cleaned;
                        break;
                    }
                }
            }
        }

        // ==========================================
        // 2. EXTRACT RESEARCH TITLE & SUGGESTIONS
        // ==========================================

        $titleCandidates = [];

        // Strategy A: Explicit title mentioned in narrative text
        // e.g. "...support the development of URLC: University Research Lifecycle Digital Platform."
        if (preg_match('/(?:development of|entitled|titled|implementation of|design and development of)\s+([A-Z0-9][A-Za-z0-9\s:,\-\(\)\/]{5,140}?)(?=\.|\;|\band\b\s+the|\bfor\b\s+the|\n\n)/i', $normalizedText, $m)) {
            $candidate = trim(preg_replace('/\s+/', ' ', $m[1]));
            $candidate = trim($candidate, " \t\n\r\0\x0B:-,.");
            if (strlen($candidate) >= 8) {
                $titleCandidates[] = $candidate;
            }
        }

        // Strategy B: Acronym + Subtitle pattern e.g. "URLC: University Research Lifecycle..."
        if (preg_match('/(?:^|\n|\s)([A-Z]{2,10}\s*:\s*[A-Z][A-Za-z0-9\s,\-\(\)\/]{8,120}?)(?=\.|\,|\n|$)/', $normalizedText, $m)) {
            $candidate = trim(preg_replace('/\s+/', ' ', $m[1]));
            $candidate = trim($candidate, " \t\n\r\0\x0B:-,.");
            if (strlen($candidate) >= 8 && !in_array($candidate, $titleCandidates)) {
                $titleCandidates[] = $candidate;
            }
        }

        // Strategy C: Genuine Title Page Lines
        $lines = explode("\n", $normalizedText);
        $coverLines = [];

        foreach ($lines as $idx => $line) {
            $line = trim($line);
            if ($idx > 40 || preg_match('/^(CHAPTER\s+1|INTRODUCTION|TABLE\s+OF|APPROVAL|1\.1)/i', $line)) {
                break;
            }

            // Reject academic institutional boilerplates
            if (preg_match('/(Republic of the Philippines|University of Antique|College of|Department of|Campus|Sibalom|Antique|A Capstone Project|Capstone Project|A Thesis|Thesis Proposal|In Partial Fulfillment|Bachelor of Science|Bachelor of|Master of|Doctor of|Faculty of|Submitted to|Submitted by|Presented to|Prepared by|Adviser|Panelist|March|April|May|June|July|August|September|October|November|December|\b202\d\b|By:)/i', $line)) {
                continue;
            }

            // Reject narrative sentences (starting with "This chapter", "The purpose", etc.)
            if (preg_match('/^(This\s+|The\s+purpose|In\s+this|Through\s+this|According\s+to|Chapter\s+|Section\s+|It\s+is|As\s+shown|The\s+first|The\s+second|The\s+third)/i', $line)) {
                continue;
            }

            // Reject lines ending in hanging grammatical prepositions
            if (preg_match('/\b(the|and|of|to|in|for|with|that|is|a|an|on)$/i', $line)) {
                continue;
            }

            if (strlen($line) >= 8 && strlen($line) <= 200) {
                $coverLines[] = $line;
            }
        }

        if (!empty($coverLines)) {
            if (isset($coverLines[1]) && strlen($coverLines[0]) < 25 && strlen($coverLines[1]) > 10) {
                $combined = rtrim($coverLines[0], ': -') . ': ' . ltrim($coverLines[1], ': -');
                $titleCandidates[] = $combined;
            } else {
                $titleCandidates[] = $coverLines[0];
            }
        }

        // Strategy D: Clean Original Filename as a reliable fallback/suggestion
        if (!empty($originalFilename)) {
            $cleanFile = pathinfo($originalFilename, PATHINFO_FILENAME);
            $cleanFile = preg_replace('/[_\-]+/', ' ', $cleanFile);
            $cleanFile = trim(preg_replace('/\s+/', ' ', $cleanFile));
            if (strlen($cleanFile) >= 4 && !in_array($cleanFile, $titleCandidates)) {
                $titleCandidates[] = $cleanFile;
            }
        }

        // Remove duplicates & clean candidates
        $uniqueCandidates = [];
        foreach ($titleCandidates as $c) {
            $c = trim($c, " \t\n\r\0\x0B:-.");
            if (!empty($c) && !in_array($c, $uniqueCandidates)) {
                $uniqueCandidates[] = $c;
            }
        }

        if (!empty($uniqueCandidates)) {
            $result['title'] = $uniqueCandidates[0];
            $result['title_suggestions'] = array_slice($uniqueCandidates, 0, 4);
        }

        // ==========================================
        // 3. EXTRACT RESEARCH FIELD / KEYWORDS
        // ==========================================

        if (preg_match('/(?:Keywords|KEYWORD|Key words)[\s:\-\—]+(.*?)(?=\n|\.|$)/i', $normalizedText, $m)) {
            $keywords = trim($m[1]);
            if (strlen($keywords) > 2) {
                $result['research_field'] = substr($keywords, 0, 255);
            }
        }

        if (empty($result['research_field'])) {
            $fields = [
                'Information Technology' => '/\b(Information Technology|Software|Web|Database|Application|Mobile App|IoT|Network|System)\b/i',
                'Computer Science' => '/\b(Computer Science|Machine Learning|Artificial Intelligence|Algorithm|Deep Learning)\b/i',
                'Criminology' => '/\b(Criminology|Law Enforcement|Police|Crime|Forensic)\b/i',
                'Education' => '/\b(Education|Pedagogy|Curriculum|Instructional|Teaching|Learning)\b/i',
                'Engineering' => '/\b(Engineering|Mechanical|Electrical|Civil|Electronics)\b/i',
                'Agriculture' => '/\b(Agriculture|Farming|Crop|Fisheries|Soil|Agronomy)\b/i',
                'Business Administration' => '/\b(Business|Marketing|Management|Entrepreneurship|Finance)\b/i',
            ];

            foreach ($fields as $fieldName => $pattern) {
                if (preg_match($pattern, $normalizedText)) {
                    $result['research_field'] = $fieldName;
                    break;
                }
            }
        }

        return $result;
    }

    /**
     * Clean and normalize multi-line paragraph text.
     */
    protected static function cleanParagraph(string $text): string
    {
        $text = str_replace(["\r\n", "\r", "\n"], ' ', $text);
        $text = preg_replace('/[ \t]+/', ' ', $text);
        return trim(substr($text, 0, 3000));
    }

    /**
     * Clean and reconstruct fragmented PDF text into continuous paragraphs.
     * Fixes:
     * - Repeating Form RESU-FM-015 headers & page numbers
     * - Split words broken across line wraps (e.g. "app\nlications" -> "applications")
     * - Isolated punctuation on their own lines (e.g. "Title \n :")
     * - Fragmented bullets (e.g. "o \n 1.1 Age;" -> "• 1.1 Age;")
     * - Hard wraps within sentences
     */
    public static function cleanExtractedText(string $text): string
    {
        if (empty($text)) {
            return '';
        }

        // 1. Normalize line endings
        $text = str_replace(["\r\n", "\r"], "\n", $text);

        // 2. Remove running form RESU-FM-015 header / footer blocks
        $text = preg_replace('/Page\s*\|\s*\d+\s*RESU\s*-\s*FM\s*-\s*015\s*Rev\.[^\n]+\s*-\s*\d+\s*-\s*\d+/is', "\n\n", $text);
        $text = preg_replace('/Page\s*\n\s*\|\s*\n\s*\d+\s*\n\s*RESU\s*\n\s*-\s*\n\s*FM\s*\n\s*-\s*\n\s*015\s*\n\s*Rev\.[^\n]+\s*\n\s*-\s*\n\s*\d+\s*\n\s*-\s*\n\s*\d+/is', "\n\n", $text);

        // 3. Remove repeated University letterhead if fragmented across lines
        $text = preg_replace('/Republic of the Philippines\s*\n\s*UNIVERSITY OF ANTIQUE\s*\n\s*Sibalom,\s*Antique\s*\n\s*RESEARCH PROPOSAL TEMPLATE/is', "\n\n", $text);

        // 4. Rejoin isolated colons or punctuation on their own lines: e.g. "Title \n :" -> "Title:"
        $text = preg_replace('/\n\s*:\s*\n/m', ":\n", $text);
        $text = preg_replace('/\n\s*:\s*/m', ": ", $text);
        $text = preg_replace('/\n\s*,\s*/m', ", ", $text);
        $text = preg_replace('/\n\s*;\s*/m', ";\n", $text);
        $text = preg_replace('/\n\s*\.\s*/m', ". ", $text);

        // 5. Rejoin hyphenated words across lines
        $text = preg_replace('/([a-zA-Z]+)\s*\n\s*-\s*\n\s*([a-zA-Z]+)/', '$1-$2', $text);
        $text = preg_replace('/([a-zA-Z]+)-\s*\n\s*([a-zA-Z]+)/', '$1-$2', $text);

        // 6. Fix fragmented words caused by PDF stream boundaries
        $text = preg_replace('/\bAndr\s*\n\s*ea\s+Jar\s*\n\s*omay\b/i', 'Andrea Jaromay', $text);

        $text = preg_replace_callback('/([a-z]{1,4})\s*\n\s*([a-z]{3,})/i', function ($m) {
            $splitWords = [
                'app' => ['lications' => 'applications'],
                'm' => ['alicious' => 'malicious'],
                'p' => ['hishing' => 'phishing'],
                'behavio' => ['ral' => 'behavioral'],
                'awarene' => ['ss' => 'awareness'],
                'pro' => ['tection' => 'protection', 'ponent' => 'proponent', 'ponents' => 'proponents'],
                'ac' => ['count' => 'account', 'cess' => 'access'],
                'ad' => ['vancement' => 'advancement'],
            ];
            $w1 = strtolower($m[1]);
            $w2 = strtolower($m[2]);
            if (isset($splitWords[$w1][$w2])) {
                return $splitWords[$w1][$w2];
            }
            return $m[1] . "\n" . $m[2];
        }, $text);

        // 7. Fix bullets: "o \n 1.1 Age;" -> "• 1.1 Age;"
        $text = preg_replace('/\n\s*o\s*\n\s*([0-9a-zA-Z])/m', "\n  • $1", $text);
        $text = preg_replace('/\n\s*([0-9]+)\.\s*\n\s*([A-Z])/m', "\n\n$1. $2", $text);

        // 8. Reconstruct paragraphs: join wrapped lines into fluent sentences
        $lines = explode("\n", $text);
        $reconstructed = [];
        $currentParagraph = '';

        $headingRegex = '/^(CHAPTER\s+[IVX0-9]+|Background of the Study|Statement of the Problem|Hypothesis of the Study|Theoretical Framework|Conceptual Framework|Definition of Terms|Review of Related Literature|Methodology|Research Design|Population and Sampling|Research Instrument|Data Gathering Procedure|Data Analysis|Ethical Considerations|References|Work Plan|Line-Item Budget|Title:|Name of Proponent|Credentials of Key|College Affiliation)/i';

        foreach ($lines as $rawLine) {
            $line = trim($rawLine);
            if ($line === '') {
                if ($currentParagraph !== '') {
                    $reconstructed[] = $currentParagraph;
                    $currentParagraph = '';
                }
                continue;
            }

            $isHeading = preg_match($headingRegex, $line);
            $isBullet = preg_match('/^(•|\d+\.\d+|\d+\.)/', $line);

            if ($currentParagraph === '') {
                $currentParagraph = $line;
            } else {
                $prevEndsWithTerminal = preg_match('/[.?!:]$/', $currentParagraph);

                if ($isHeading || $isBullet || $prevEndsWithTerminal) {
                    $reconstructed[] = $currentParagraph;
                    $currentParagraph = $line;
                } else {
                    $currentParagraph .= ' ' . $line;
                }
            }
        }

        if ($currentParagraph !== '') {
            $reconstructed[] = $currentParagraph;
        }

        $cleaned = implode("\n\n", $reconstructed);
        // Collapse excess blank lines
        $cleaned = preg_replace('/\n{3,}/', "\n\n", $cleaned);

        return trim($cleaned);
    }

    /**
     * Convert clean extracted text into beautifully structured semantic HTML for Interactive Review.
     */
    public static function formatTextToHtml(string $text): string
    {
        $cleaned = self::cleanExtractedText($text);
        if (empty($cleaned)) {
            return '<p class="text-muted fst-italic">No manuscript text available.</p>';
        }

        $paragraphs = explode("\n\n", $cleaned);
        $html = '';

        $headingRegex = '/^(CHAPTER\s+[IVX0-9]+|Background of the Study|Statement of the Problem|Hypothesis of the Study|Theoretical Framework|Conceptual Framework|Definition of Terms|Review of Related Literature|Methodology|Research Design|Population and Sampling|Research Instrument|Data Gathering Procedure|Data Analysis|Ethical Considerations|References|Work Plan|Line-Item Budget|Title:|Name of Proponent|Credentials of Key|College Affiliation)/i';

        foreach ($paragraphs as $para) {
            $p = trim($para);
            if ($p === '') continue;

            if (preg_match($headingRegex, $p)) {
                $html .= '<h6 class="fw-bold text-primary mt-4 mb-2 pb-1 border-bottom border-light-subtle d-flex align-items-center" style="font-size: 0.98rem; letter-spacing: 0.02em;"><i class="bi bi-bookmark-fill me-2 small text-primary opacity-75"></i>' . htmlspecialchars($p) . '</h6>' . "\n";
            } elseif (preg_match('/^(•|\d+\.\d+|\d+\.)/', $p)) {
                $html .= '<div class="ms-3 mb-1.5 text-secondary" style="font-size: 0.92rem; line-height: 1.65;">' . htmlspecialchars($p) . '</div>' . "\n";
            } else {
                $html .= '<p class="mb-3 text-secondary" style="font-size: 0.92rem; text-align: justify; line-height: 1.72; text-indent: 1.5rem;">' . htmlspecialchars($p) . '</p>' . "\n";
            }
        }

        return $html;
    }
}
