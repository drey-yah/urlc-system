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
            // Pattern 2: Explicit Executive Summary, Project Overview, Project Description
            '/(?:Executive Summary|Project Overview|Project Description)[\s:\-\—\.\d]*\n+([\s\S]+?)(?=\n\s*(?:1\.\d|\bObjectives\b|\bScope\b|\bSignificance\b|\bCHAPTER\s+2|\bMethodology\b|\Z))/i',
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

        // Pattern 3: Look for focused study proposal thesis statement (e.g. "This study proposes the development...")
        if (empty($result['abstract'])) {
            if (preg_match('/(This (?:study|research|project) (?:proposes|aims|investigates|develops|presents)[\s\S]+?\.)(?=\n\n|\Z)/i', $normalizedText, $m)) {
                $cand = self::cleanParagraph($m[1]);
                if (strlen($cand) >= 70) {
                    $result['abstract'] = $cand;
                }
            }
        }

        // Pattern 4: Fallback to Background / Context paragraph (stripping header labels)
        if (empty($result['abstract'])) {
            if (preg_match('/(?:Project Context|Background of the Study)[\s:\-\—\.\d]*\n+([\s\S]+?)(?=\n\s*(?:1\.\d|Statement of the Problem|\bObjectives\b|\bScope\b|\bSignificance\b|\bCHAPTER\s+2|\bMethodology\b|\Z))/i', $normalizedText, $m)) {
                $cand = self::cleanParagraph($m[1]);
                $cand = preg_replace('/^(?:Background of the Study|Project Context|Rationale)[\s:\-\.]*/i', '', $cand);
                if (strlen($cand) >= 50) {
                    $result['abstract'] = $cand;
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
        $headerSlice = substr($normalizedText, 0, 3000);

        // Strategy A: Official Form RESU-FM-015 explicit Title header (e.g. "Title : Development and Evaluation of...")
        if (preg_match('/(?:^|\n|\b)(?:Title|RESEARCH TITLE|PROJECT TITLE)\s*[:\-]?\s*\n*([\s\S]+?)(?=\n\s*(?:Name of Proponent|Credentials of Key|College Affiliation|Background of the Study|CHAPTER|1\.|\Z))/i', $headerSlice, $m)) {
            $clean = trim(preg_replace('/\s+/', ' ', $m[1]), " \t\n\r\0\x0B:-,.");
            if (strlen($clean) >= 8 && strlen($clean) <= 250) {
                $titleCandidates[] = $clean;
            }
        }

        // Strategy B: Acronym + Subtitle pattern e.g. "URLC: University Research Lifecycle..."
        if (preg_match('/(?:^|\n|\s)([A-Z]{2,10}\s*:\s*[A-Z][A-Za-z0-9\s,\-\(\)\/]{8,120}?)(?=\.|\,|\n|$)/', $headerSlice, $m)) {
            $candidate = trim(preg_replace('/\s+/', ' ', $m[1]));
            $candidate = trim($candidate, " \t\n\r\0\x0B:-,.");
            if (strlen($candidate) >= 8 && !in_array($candidate, $titleCandidates)) {
                $titleCandidates[] = $candidate;
            }
        }

        // Strategy C: Genuine Title Page Lines (first 40 lines of manuscript cover)
        $lines = explode("\n", $normalizedText);
        $coverLines = [];

        foreach ($lines as $idx => $line) {
            $line = trim($line);
            if ($idx > 40 || preg_match('/^(CHAPTER\s+1|INTRODUCTION|TABLE\s+OF|APPROVAL|1\.1)/i', $line)) {
                break;
            }

            // Reject academic institutional boilerplates
            if (preg_match('/(Republic of the Philippines|University of Antique|College of|Department of|Campus|Sibalom|Antique|A Capstone Project|Capstone Project|A Thesis|Thesis Proposal|In Partial Fulfillment|Bachelor of Science|Bachelor of|Master of|Doctor of|Faculty of|Submitted to|Submitted by|Presented to|Prepared by|Adviser|Panelist|March|April|May|June|July|August|September|October|November|December|\b202\d\b|By:|Name of Proponent|Credentials of|College Affiliation)/i', $line)) {
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
                if (!in_array($combined, $titleCandidates)) {
                    $titleCandidates[] = $combined;
                }
            } elseif (!in_array($coverLines[0], $titleCandidates)) {
                $titleCandidates[] = $coverLines[0];
            }
        }

        // Strategy D: Explicit narrative title ("entitled" or "titled") in quotes or explicit Title Case
        if (preg_match('/(?:entitled|titled)\s+["“]([^"”]{8,150})["”]/i', $normalizedText, $m)) {
            $cand = trim(preg_replace('/\s+/', ' ', $m[1]), " \t\n\r\0\x0B:-,.");
            if (strlen($cand) >= 8 && !in_array($cand, $titleCandidates)) {
                $titleCandidates[] = $cand;
            }
        }

        // Strategy E: Clean Original Filename as a reliable fallback/suggestion
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

        // 6. Fix fragmented words caused by PDF stream boundaries or justification spacing
        $text = preg_replace('/\bAndr\s*\n\s*ea\s+Jar\s*\n\s*omay\b/i', 'Andrea Jaromay', $text);
        $text = preg_replace('/\besta\s+blished\b/i', 'established', $text);
        $text = preg_replace('/\brecommen\s+dations\b/i', 'recommendations', $text);
        $text = preg_replace('/\baut\s+horized\b/i', 'authorized', $text);
        $text = preg_replace('/\bin\s+cluded\b/i', 'included', $text);
        $text = preg_replace('/\bmanagemen\s+t\b/i', 'management', $text);
        $text = preg_replace('/\badministrativ\s+e\b/i', 'administrative', $text);
        $text = preg_replace('/\bn\s+ot\b/i', 'not', $text);
        $text = preg_replace('/\bC\s+CIS\b/i', 'CCIS', $text);

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

    /**
     * Parse continuous manuscript text into modular, structured, individually editable sections.
     *
     * @param string $text
     * @return array
     */
    public static function parseManuscriptSections(string $text): array
    {
        $cleaned = self::cleanExtractedText($text);
        if (empty($cleaned)) {
            return [];
        }

        $headingDefinitions = [
            [
                'title' => 'Document Header & Submission Info',
                'key' => 'document_header',
                'icon' => 'bi-file-earmark-text',
                'field' => null,
                'aliases' => ['RESU-FM\s*-\s*015[^\n]*', 'Republic of the Philippines', 'UNIVERSITY OF ANTIQUE'],
            ],
            [
                'title' => 'Research Title & Identification',
                'key' => 'title',
                'icon' => 'bi-journal-bookmark-fill',
                'field' => 'title',
                'aliases' => ['Title\s*:', 'RESEARCH TITLE'],
            ],
            [
                'title' => 'Proponents & Key Personnel',
                'key' => 'credentials',
                'icon' => 'bi-people-fill',
                'field' => 'credentials',
                'aliases' => ['Name of Proponent\/s?', 'Credentials of Key Personnel', 'College Affiliation', 'KEY PROPONENT'],
            ],
            [
                'title' => 'Background of the Study',
                'key' => 'background_of_study',
                'icon' => 'bi-bookmark-fill',
                'field' => 'background_of_study',
                'aliases' => ['Background of the Study', 'CHAPTER\s+1\s*:?\s*Background', 'Rationale of the Study', 'Rationale'],
            ],
            [
                'title' => 'Statement of the Problem',
                'key' => 'statement_of_problem',
                'icon' => 'bi-question-diamond-fill',
                'field' => null,
                'aliases' => ['Statement of the Problem', 'Research Questions', 'Objectives of the Study'],
            ],
            [
                'title' => 'Hypothesis of the Study',
                'key' => 'hypothesis',
                'icon' => 'bi-lightbulb-fill',
                'field' => null,
                'aliases' => ['Hypothesis of the Study', 'Hypothesis'],
            ],
            [
                'title' => 'Theoretical & Conceptual Framework',
                'key' => 'theoretical_framework',
                'icon' => 'bi-diagram-2-fill',
                'field' => null,
                'aliases' => ['Theoretical Framework', 'Conceptual Framework', 'Theoretical and Conceptual Framework'],
            ],
            [
                'title' => 'Significance of the Study',
                'key' => 'significance_of_study',
                'icon' => 'bi-award-fill',
                'field' => 'significance_of_study',
                'aliases' => ['Significance of the Study', 'Significance and Impact'],
            ],
            [
                'title' => 'Scope & Delimitation',
                'key' => 'scope_delimitation',
                'icon' => 'bi-arrows-angle-contract',
                'field' => null,
                'aliases' => ['Scope and Delimitation', 'Scope of the Study', 'Delimitations of the Study'],
            ],
            [
                'title' => 'Definition of Terms',
                'key' => 'definition_of_terms',
                'icon' => 'bi-spellcheck',
                'field' => 'definition_of_terms',
                'aliases' => ['Definition of Terms', 'Operational Definitions', 'Definition of Variables'],
            ],
            [
                'title' => 'Review of Related Literature',
                'key' => 'literature_review',
                'icon' => 'bi-book-half',
                'field' => 'literature_review',
                'aliases' => ['Review of Related Literature', 'CHAPTER\s+2', 'Related Literature and Studies', 'Related Studies'],
            ],
            [
                'title' => 'Methodology & Research Design',
                'key' => 'methodology',
                'icon' => 'bi-diagram-3-fill',
                'field' => 'methodology',
                'aliases' => ['Methodology', 'Research Methodology', 'Research Design', 'CHAPTER\s+3'],
            ],
            [
                'title' => 'Population & Sampling',
                'key' => 'population_sampling',
                'icon' => 'bi-people',
                'field' => null,
                'aliases' => ['Population and Sampling', 'Sampling Technique', 'Respondents of the Study', 'Participants of the Study', 'Study Locale and Participants'],
            ],
            [
                'title' => 'Research Instrumentation',
                'key' => 'instrumentation',
                'icon' => 'bi-ui-checks-grid',
                'field' => null,
                'aliases' => ['Research Instrument', 'Research Instrumentation', 'Data Collection Tools', 'Validation of the Instrument'],
            ],
            [
                'title' => 'Data Gathering Procedure',
                'key' => 'data_gathering',
                'icon' => 'bi-clipboard-data-fill',
                'field' => null,
                'aliases' => ['Data Gathering Procedure', 'Data Collection Procedure'],
            ],
            [
                'title' => 'Data Analysis & Statistical Tools',
                'key' => 'data_analysis',
                'icon' => 'bi-graph-up-arrow',
                'field' => null,
                'aliases' => ['Data Analysis', 'Statistical Treatment of Data', 'Statistical Analysis'],
            ],
            [
                'title' => 'Ethical Considerations & Data Privacy',
                'key' => 'ethical_considerations',
                'icon' => 'bi-shield-check',
                'field' => null,
                'aliases' => ['Ethical Considerations', 'Data Privacy and Ethics', 'Ethics Clearance'],
            ],
            [
                'title' => 'References & Bibliography',
                'key' => 'references',
                'icon' => 'bi-quote',
                'field' => 'references',
                'aliases' => ['References', 'Literature Cited', 'Bibliography', 'Works Cited'],
            ],
            [
                'title' => 'Work Plan & Schedule',
                'key' => 'work_plan',
                'icon' => 'bi-calendar3-range',
                'field' => null,
                'aliases' => ['Work Plan', 'Schedule of Activities', 'Gantt Chart', 'Project Timeline'],
            ],
            [
                'title' => 'Financial Plan & Budget',
                'key' => 'budget',
                'icon' => 'bi-cash-coin',
                'field' => null,
                'aliases' => ['Financial Plan', 'Line-Item Budget', 'Budgetary Requirements', 'Budget Plan'],
            ],
        ];

        // Build composite aliases regex
        $allAliases = [];
        foreach ($headingDefinitions as $def) {
            foreach ($def['aliases'] as $alias) {
                $allAliases[] = $alias;
            }
        }
        $allAliasesRegex = implode('|', $allAliases);

        $normalized = str_replace(["\r\n", "\r"], "\n", $cleaned);
        // Ensure double newlines before and after headings when joined to text on the same line
        $normalized = preg_replace('#(?:\n|\A)((' . $allAliasesRegex . ')(?:\s*[:\-])?)\s*([A-Z0-9].*)#', "\n\n$1\n\n$3", $normalized);

        $paragraphs = preg_split('/\n\s*\n/', $normalized);
        $rawSections = [];
        $currentDef = [
            'title' => 'Manuscript Overview',
            'key' => 'manuscript_overview',
            'icon' => 'bi-file-earmark-text',
            'field' => null,
        ];
        $currentHeading = '';
        $currentContent = [];

        foreach ($paragraphs as $para) {
            $trimmed = trim($para);
            if ($trimmed === '') continue;

            $matchedDef = null;
            $matchedHeading = null;

            foreach ($headingDefinitions as $def) {
                foreach ($def['aliases'] as $alias) {
                    if (preg_match('#^(' . $alias . ')(?:\s*[:\-])?\s*$#', $trimmed)) {
                        $matchedDef = $def;
                        $matchedHeading = $trimmed;
                        break 2;
                    }
                }
            }

            if ($matchedDef) {
                if (!empty($currentContent)) {
                    $rawSections[] = [
                        'key' => $currentDef['key'],
                        'title' => $currentDef['title'],
                        'icon' => $currentDef['icon'],
                        'field' => $currentDef['field'],
                        'heading' => $currentHeading,
                        'content' => implode("\n\n", $currentContent),
                    ];
                    $currentContent = [];
                }
                $currentDef = $matchedDef;
                $currentHeading = $matchedHeading;
            } else {
                $currentContent[] = $trimmed;
            }
        }

        if (!empty($currentContent)) {
            $rawSections[] = [
                'key' => $currentDef['key'],
                'title' => $currentDef['title'],
                'icon' => $currentDef['icon'],
                'field' => $currentDef['field'],
                'heading' => $currentHeading,
                'content' => implode("\n\n", $currentContent),
            ];
        }

        if (empty($rawSections)) {
            return [
                [
                    'index' => 0,
                    'key' => 'extracted_text',
                    'title' => 'Manuscript Content',
                    'icon' => 'bi-journal-text',
                    'field' => 'extracted_text',
                    'heading' => '',
                    'content' => $cleaned,
                ]
            ];
        }

        // Merge consecutive sections sharing the same key
        $merged = [];
        foreach ($rawSections as $sec) {
            if (empty($merged)) {
                $merged[] = $sec;
                continue;
            }
            $lastIdx = count($merged) - 1;
            if ($merged[$lastIdx]['key'] === $sec['key']) {
                $mergedHeading = trim($merged[$lastIdx]['heading'] . (!empty($sec['heading']) ? "\n" . $sec['heading'] : ''));
                $mergedContent = trim($merged[$lastIdx]['content'] . "\n\n" . (!empty($sec['heading']) ? $sec['heading'] . "\n\n" : '') . $sec['content']);
                $merged[$lastIdx]['heading'] = $mergedHeading;
                $merged[$lastIdx]['content'] = $mergedContent;
            } else {
                $merged[] = $sec;
            }
        }

        // Assign sequential zero-based indexes
        foreach ($merged as $i => &$m) {
            $m['index'] = $i;
        }

        return $merged;
    }

    /**
     * Recombine parsed sections back into continuous clean extracted text.
     *
     * @param array $sections
     * @return string
     */
    public static function recombineSections(array $sections): string
    {
        $parts = [];
        foreach ($sections as $s) {
            $heading = !empty($s['heading']) ? trim($s['heading']) : (!empty($s['title']) ? trim($s['title']) : '');
            $content = trim($s['content'] ?? '');

            if (!empty($heading) && !empty($content)) {
                if (stripos($content, $heading) === 0) {
                    $parts[] = $content;
                } else {
                    $parts[] = $heading . "\n\n" . $content;
                }
            } elseif (!empty($content)) {
                $parts[] = $content;
            } elseif (!empty($heading)) {
                $parts[] = $heading;
            }
        }
        return implode("\n\n", $parts);
    }

    /**
     * Format paragraph text inside a single section to clean readable HTML.
     *
     * @param string $content
     * @return string
     */
    public static function formatSectionContentHtml(string $content): string
    {
        $content = trim($content);
        if (empty($content)) {
            return '<p class="text-muted fst-italic">No content provided for this section.</p>';
        }

        $paragraphs = preg_split('/\n\s*\n/', $content);
        $html = '';

        foreach ($paragraphs as $para) {
            $p = trim($para);
            if ($p === '') continue;

            if (preg_match('/^(•|\d+\.\d+|\d+\.)/', $p)) {
                $html .= '<div class="ms-3 mb-2 text-secondary" style="font-size: 0.92rem; line-height: 1.68;">' . nl2br(htmlspecialchars($p)) . '</div>' . "\n";
            } elseif (preg_match('/^[A-Z0-9\s:,\-\(\)\/]{3,60}:$/', $p)) {
                $html .= '<h6 class="fw-bold text-dark mt-3 mb-1.5" style="font-size: 0.92rem;">' . htmlspecialchars($p) . '</h6>' . "\n";
            } else {
                $html .= '<p class="mb-3 text-secondary" style="font-size: 0.92rem; text-align: justify; line-height: 1.72; text-indent: 1.5rem;">' . nl2br(htmlspecialchars($p)) . '</p>' . "\n";
            }
        }

        return $html;
    }

    /**
     * Parse and extract Line-Item Budget (LIB) table rows from extracted proposal PDF text.
     * Accurately supports University of Antique Form RESU-FM-015 format (MOOE and Capital Outlay)
     * as well as standard Philippine DOST / academic research budget matrices.
     *
     * @param string $text
     * @return array Array of budget items: ['category_type', 'category_group', 'item_name', 'existing_resources', 'amount']
     */
    public static function parseBudgetItems(string $text): array
    {
        $items = [];
        if (empty($text)) {
            return $items;
        }

        // 1. Locate Line-Item Budget / Financial Plan section
        $startRegex = '/(?:Line[\s\-]Item\s+Budget|Financial\s+Plan|Budgetary\s+Requirements|I\.\s*Maintenance\s+and\s+Other\s+Operating\s+Expenses)/i';
        if (!preg_match($startRegex, $text, $m, PREG_OFFSET_CAPTURE)) {
            return $items;
        }

        $offset = $m[0][1];
        $remaining = substr($text, $offset);

        // Terminate at next major proposal section or footer note
        $endRegex = '/(?:\n\s*(?:Work\s+Plan|Gantt\s+Chart|Schedule\s+of\s+Activities|Submitted\s+by|Prepared\s+by|Approval\s+Sheet|Endorsement|Form\s+RESU|CHAPTER\s+[IVX0-9]+)\b|Note:\s*Do\s+not\s+merge)/i';
        if (preg_match($endRegex, $remaining, $endM, PREG_OFFSET_CAPTURE)) {
            $budgetSlice = substr($remaining, 0, $endM[0][1]);
        } else {
            $budgetSlice = substr($remaining, 0, 4000);
        }

        // Category definitions in RESU-FM-015
        $categories = [
            ['key' => 'co', 'group' => 'capital_outlay', 'regex' => '/(?:II\.\s*)?Capital\s+Outlay/i'],
            ['key' => 'mooe', 'group' => 'supplies', 'regex' => '/A\.\s*Supplies\s+and\s+Materials(?:\s+Expenses)?/i'],
            ['key' => 'mooe', 'group' => 'other_mooe', 'regex' => '/B\.\s*Semi[\s\-]Expenda?ble/i'],
            ['key' => 'mooe', 'group' => 'travel', 'regex' => '/C\.\s*Travelling\s+Expenses(?:\s*\(Local\))?/i'],
            ['key' => 'mooe', 'group' => 'travel', 'regex' => '/D\.\s*Transportation/i'],
            ['key' => 'mooe', 'group' => 'professional_services', 'regex' => '/E\.\s*Other\s+Professional\s+Services/i'],
            ['key' => 'mooe', 'group' => 'other_mooe', 'regex' => '/F\.\s*Other\s+MOOE/i'],
            ['key' => 'subtotal', 'group' => 'subtotal', 'regex' => '/Sub[\s\-]total\s+for\s+MOOE/i'],
            ['key' => 'subtotal', 'group' => 'subtotal', 'regex' => '/Sub[\s\-]total\s+for\s+CO/i'],
            ['key' => 'subtotal', 'group' => 'subtotal', 'regex' => '/GRAND\s+TOTAL/i'],
        ];

        // Insert category block markers
        $markedText = $budgetSlice;
        foreach ($categories as $idx => $c) {
            $markedText = preg_replace_callback($c['regex'], function ($match) use ($idx) {
                return "\n\n___CAT_{$idx}___\n" . $match[0] . "\n";
            }, $markedText);
        }

        $blocks = explode('___CAT_', $markedText);

        foreach ($blocks as $block) {
            $block = trim($block);
            if (empty($block)) continue;

            if (preg_match('/^(\d+)___\s*\n(.*)$/s', $block, $bMatch)) {
                $catIdx = intval($bMatch[1]);
                $catMeta = $categories[$catIdx];
                $catBody = trim($bMatch[2]);

                // Remove the header line
                $lines = explode("\n", $catBody);
                array_shift($lines);
                $bodyText = implode("\n", $lines);

                $catKey = $catMeta['key'];
                $catGroup = $catMeta['group'];

                // Special handling: equipment/laptop between Sub-total for MOOE and Sub-total for CO
                if ($catKey === 'subtotal' && preg_match('/\b(laptop|computer|desktop|equipment|hardware|printer)\b/i', $bodyText)) {
                    $catKey = 'co';
                    $catGroup = 'capital_outlay';
                } elseif ($catKey === 'subtotal') {
                    continue;
                }

                $parsed = self::extractBudgetItemsFromBody($bodyText, $catGroup, $catKey);
                foreach ($parsed as $item) {
                    $items[] = $item;
                }
            }
        }

        // Fallback: If no categories matched but numbers & items exist in the budget slice
        if (empty($items)) {
            $items = self::fallbackExtractBudgetItems($budgetSlice);
        }

        return $items;
    }

    /**
     * Extract individual budget line-items from a category body text.
     */
    protected static function extractBudgetItemsFromBody(string $bodyText, string $currentGroup, string $currentType): array
    {
        $items = [];

        // Ignore placeholder text without numeric amounts
        if (preg_match('/(?:Just\s+add\s+the\s+row|No\s+new\s+equipment)/i', $bodyText) && !preg_match('/[1-9][0-9]{2,}\.[0-9]{2}/', $bodyText)) {
            return $items;
        }

        // Clean template boilerplate
        $cleanBody = preg_replace('/\(Just\s+add\s+the\s+row[^\)]*\)/i', '', $bodyText);
        $cleanBody = preg_replace('/No\s+new\s+equipment\s+requested;[^\n\.]*/i', '', $cleanBody);

        // Split by numbering (a.1, a.2, 1., 2.) if present
        $hasSubItems = preg_match('/(?:^|\n|\s)([a-z]\.[0-9]+|[0-9]+\.)\s+/i', $cleanBody);

        if ($hasSubItems) {
            $parts = preg_split('/(?=(?:^|\n|\s)(?:[a-z]\.[0-9]+|[0-9]+\.)\s+)/i', $cleanBody);
        } else {
            $lines = explode("\n", $cleanBody);
            $parts = [];
            $currentPart = '';

            foreach ($lines as $l) {
                $t = trim($l);
                if (empty($t)) continue;

                if ($currentPart !== '' && preg_match('/[0-9]{1,3}(?:,[0-9]{3})*\.[0-9]{2}/', $currentPart) && !preg_match('/^[0-9]{1,3}(?:,[0-9]{3})*\.[0-9]{2}$/', $t)) {
                    $parts[] = $currentPart;
                    $currentPart = $t;
                } else {
                    $currentPart .= ($currentPart === '' ? '' : ' ') . $t;
                }
            }
            if ($currentPart !== '') {
                $parts[] = $currentPart;
            }
        }

        foreach ($parts as $part) {
            $part = trim($part);
            if (empty($part)) continue;

            if (preg_match('/^(\([a-z0-9\.\s]+\)|Just\s+add\s+the\s+row|No\s+new\s+equipment|Sub[\s\-]total|Grand\s+Total)/i', $part)) {
                continue;
            }

            // Inline multi-item check: e.g. "Food ... 2,500.00 Token ... 500.00"
            if (preg_match_all('/([A-Za-z][A-Za-z0-9\s\(\)@:\-\[\]\.\+=\/]{3,80}?)\s+(?:Php\s*|₱\s*)?([0-9]{1,3}(?:,[0-9]{3})*\.[0-9]{2})/i', $part, $inlineMatches, PREG_SET_ORDER)) {
                if (count($inlineMatches) >= 2) {
                    $inlineItems = [];
                    foreach ($inlineMatches as $im) {
                        $iName = trim(preg_replace('/\s+/', ' ', $im[1]), " \t\n\r\0\x0B:-,;.");
                        $iCost = floatval(str_replace(',', '', $im[2]));
                        if ($iCost > 0 && strlen($iName) >= 3 && !preg_match('/^(?:RESU|College|Proposed|Sub[\s\-]total|Total)/i', $iName)) {
                            $inlineItems[] = [
                                'category_type' => $currentType,
                                'category_group' => $currentGroup,
                                'item_name' => $iName,
                                'existing_resources' => 'RESU',
                                'amount' => $iCost,
                            ];
                        }
                    }
                    if (!empty($inlineItems)) {
                        foreach ($inlineItems as $ii) {
                            $items[] = $ii;
                        }
                        continue;
                    }
                }
            }

            // Standard line item extraction
            if (preg_match_all('/(?:Php\s*|₱\s*)?([0-9]{1,3}(?:,[0-9]{3})*(?:\.[0-9]{2}))\b/i', $part, $amtMatches)) {
                $amounts = $amtMatches[1];
                $numAmounts = array_map(function($a) { return floatval(str_replace(',', '', $a)); }, $amounts);
                $validAmounts = array_values(array_filter($numAmounts, function($a) { return $a > 0; }));
                if (empty($validAmounts)) continue;

                $cost = end($validAmounts);

                // Clean description
                $desc = preg_replace('/(?:Php\s*|₱\s*)?[0-9]{1,3}(?:,[0-9]{3})*(?:\.[0-9]{2})/i', '', $part);
                $existingRes = 'RESU';
                if (preg_match('/(?:Proposed:\s*)?(RESU|College|UA|Department)\b/i', $desc, $resMatch)) {
                    $existingRes = $resMatch[1];
                    $desc = preg_replace('/(?:Proposed:\s*)?(RESU|College|UA|Department)\b/i', '', $desc);
                }

                // Strip sub-item prefixes
                $desc = preg_replace('/^[a-z]\.[0-9]+\s*/i', '', $desc);
                $desc = preg_replace('/^[0-9]+\.\s*/', '', $desc);
                $desc = trim(preg_replace('/\s+/', ' ', $desc), " \t\n\r\0\x0B:-,;.");

                if (strlen($desc) >= 3) {
                    $catType = $currentType;
                    $catGroup = $currentGroup;
                    if (preg_match('/\b(laptop|computer|desktop|server|equipment|projector|hardware)\b/i', $desc)) {
                        $catType = 'co';
                        $catGroup = 'capital_outlay';
                    }

                    $items[] = [
                        'category_type' => $catType,
                        'category_group' => $catGroup,
                        'item_name' => $desc,
                        'existing_resources' => $existingRes,
                        'amount' => $cost,
                    ];
                }
            }
        }

        return $items;
    }

    /**
     * Fallback extractor for unformatted or non-RESU-FM-015 budget tables.
     */
    protected static function fallbackExtractBudgetItems(string $text): array
    {
        $items = [];
        $lines = explode("\n", $text);

        foreach ($lines as $line) {
            $t = trim($line);
            if (empty($t) || preg_match('/^(?:Total|Sub[\s\-]total|Grand\s+Total|Line[\s\-]Item|Expense|Proposed)/i', $t)) {
                continue;
            }

            if (preg_match('/(?:Php\s*|₱\s*)?([0-9]{1,3}(?:,[0-9]{3})*\.[0-9]{2})\b/i', $t, $m)) {
                $cost = floatval(str_replace(',', '', $m[1]));
                if ($cost <= 0) continue;

                $name = preg_replace('/(?:Php\s*|₱\s*)?[0-9]{1,3}(?:,[0-9]{3})*\.[0-9]{2}/i', '', $t);
                $name = trim(preg_replace('/\s+/', ' ', $name), " \t\n\r\0\x0B:-,;.");

                if (strlen($name) >= 3) {
                    $isCo = preg_match('/\b(equipment|laptop|computer|printer|server|hardware|machinery|device)\b/i', $name);
                    $items[] = [
                        'category_type' => $isCo ? 'co' : 'mooe',
                        'category_group' => $isCo ? 'capital_outlay' : 'supplies',
                        'item_name' => $name,
                        'existing_resources' => 'RESU',
                        'amount' => $cost,
                    ];
                }
            }
        }

        return $items;
    }
}
