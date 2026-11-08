# UNIVERSITY RESEARCH LIFECYCLE – DIGITAL PLATFORM (URLC SYSTEM)
## Chapter IV: Results and Discussion & Chapter V: Summary, Conclusion, and Recommendations

---

# CHAPTER IV
# RESULT AND DISCUSSION

## Result

To evaluate the overall software quality, technical performance, and operational acceptability of the **University Research Lifecycle – Digital Platform (URLC System)**, a formal empirical evaluation was conducted utilizing two recognized software quality frameworks: **McCall’s Software Quality Model** and the **ISO/IEC 25010:2011 Software Product Quality Standard**. These frameworks provide a complementary, dual-perspective evaluation: McCall’s model focuses on rigorous technical and architectural software engineering attributes evaluated by IT experts, while the ISO/IEC 25010 model measures functional, operational, and user-experience characteristics evaluated by end-users directly involved in the university research lifecycle.

The evaluation respondents comprised two dedicated groups:
1. **Three (3) IT Experts:** Faculty members and software specialists from the **College of Computing and Information Sciences (CCIS), University of Antique**, who scrutinized the system's technical design, architectural robustness, database schema, security mechanisms, and code maintainability based on McCall’s Software Quality Model.
2. **Thirty (30) End-User Stakeholders:** Purposively selected respondents representing the full cross-section of institutional actors at the **University of Antique**, including Faculty Researchers, College Research Coordinators, College Deans, Support Staff (Receiving & Recording), Technical Reviewers/Evaluators, Budget Officers, and Research Administrators. These respondents evaluated the platform’s real-world usability, functional suitability, performance efficiency, and security using an ISO/IEC 25010 questionnaire.

Both evaluation instruments employed a standard 5-point Likert Scale. The statistical scoring range and corresponding qualitative interpretations adopted in this study are detailed in Table 1.

### Table 1. Software Quality Evaluation Rating Scale and Verbal Interpretation
| Scale / Range | Descriptive Rating | Verbal Interpretation (Agreement) | Qualitative Significance |
| :---: | :---: | :---: | :--- |
| **4.21 – 5.00** | **Excellent** | **Strongly Agree** | Outstanding quality; fully meets and exceeds institutional standards. |
| **3.41 – 4.20** | **Very Good** | **Agree** | High quality; satisfies operational criteria with minor cosmetic enhancements. |
| **2.61 – 3.40** | **Good** | **Neutral** | Acceptable quality; baseline functional requirements are met. |
| **1.81 – 2.60** | **Fair** | **Disagree** | Below standard; requires structural modifications and optimizations. |
| **1.00 – 1.80** | **Poor** | **Strongly Disagree** | Unacceptable quality; fails to satisfy core software engineering requirements. |

---

### 1. Expert Evaluation Results (McCall’s Software Quality Model)

Three (3) IT Experts evaluated the technical foundation of the URLC System using McCall’s Software Quality Model across its core dimensions: Product Revision (Maintainability, Flexibility, Testability), Product Transition (Portability, Reusability, Interoperability), and Product Operation (Correctness, Reliability, Efficiency, Integrity, Usability). The maximum feasible score per expert was 95 points.

Table 2 presents the individual score summations, mean scores, percentage quality ratings, and qualitative interpretations generated from the expert evaluation.

### Table 2. Summary of McCall’s Software Quality Evaluation by IT Experts
| IT Expert Evaluator | Total Score Summation | Maximum Feasible Score | Mean Score | Percentage Quality Rating (%) | Verbal Interpretation |
| :---: | :---: | :---: | :---: | :---: | :---: |
| **Expert Evaluator 1** | 91 | 95 | 4.79 | 96% | Excellent |
| **Expert Evaluator 2** | 89 | 95 | 4.68 | 94% | Excellent |
| **Expert Evaluator 3** | 69 | 95 | 3.63 | 73% | Very Good |
| **Grand Mean / Overall** | **249 (Total)** | **285 (Total)** | **4.37** | **87.67%** | **Excellent** |

As presented in Table 2, **Expert 1** awarded the system a total score of 91 out of 95 (Mean: 4.79, 96% Quality), denoting an "Excellent" rating with highest commendations for the platform's multi-tier MVC architecture, automated state routing, and robust PostgreSQL normalization. **Expert 2** rated the system with 89 out of 95 (Mean: 4.68, 94% Quality), also interpreted as "Excellent," citing the clean separation of concerns, effective implementation of Spatie ActivityLog audit trails, and seamless Flysystem S3 cloud storage integration. **Expert 3** provided a total score of 69 out of 95 (Mean: 3.63, 73% Quality), interpreted as "Very Good," specifically noting areas where future offline resilience and external third-party API hooks (such as external Turnitin integration) could be further strengthened.

Overall, the three IT experts gave the URLC System a combined score of **249 out of 285**, yielding a **Grand Mean of 4.37** and an **Average Percentage Quality Rating of 87.67%**, which firmly establishes an **"Excellent"** software quality classification under McCall’s Software Quality Framework.

```
========================================================================================
                      McCall's Software Quality Evaluation - IT Experts
========================================================================================
  Expert 1: [████████████████████████████████████████] 4.79 (96%) - Excellent
  Expert 2: [██████████████████████████████████████░] 4.68 (94%) - Excellent
  Expert 3: [██████████████████████████████░░░░░░░░░] 3.63 (73%) - Very Good
----------------------------------------------------------------------------------------
  Grand Mean: 4.37 / 5.00  |  Overall Quality: 87.67%  |  Interpretation: EXCELLENT
========================================================================================
```

---

### 2. End-User Evaluation Results (ISO/IEC 25010 Software Quality Standard)

The platform was subjected to comprehensive user acceptance testing and evaluation by thirty (30) respondents representing institutional stakeholders across the university. The evaluation instrument comprised twenty-five (25) standardized indicator statements mapped across the eight (8) quality characteristics of the ISO/IEC 25010 model: **Functional Suitability, Performance Efficiency, Compatibility, Usability, Reliability, Security, Maintainability, and Portability**.

Table 3 details the empirical computation results for each question, including the itemized weighted mean, standard deviation, and verbal interpretation.

### Table 3. ISO/IEC 25010 Itemized Computation Results (N = 30 Respondents)
| Question ID | Evaluated Indicator Statement / Characteristic | Mean ($\bar{x}$) | Standard Deviation ($SD$) | Verbal Interpretation |
| :---: | :--- | :---: | :---: | :---: |
| **Q1** | The platform provides complete functional coverage for proposal creation, manuscript uploading, and 5-phase research lifecycle monitoring. *(Functional Completeness)* | 4.60 | 0.55 | Strongly Agree / Excellent |
| **Q2** | The system processes proposal submissions, endorsements, reviewer rubric scoring, and administrative approvals accurately and without logical errors. *(Functional Correctness)* | 4.47 | 0.62 | Strongly Agree / Excellent |
| **Q3** | The platform functions and administrative checkpoints are fully appropriate and aligned with the institutional policies of the University Research Unit. *(Functional Appropriateness)* | 4.53 | 0.62 | Strongly Agree / Excellent |
| **Q4** | System pages, data tables, and role dashboards render quickly and respond promptly during user interactions. *(Time Behavior)* | 4.60 | 0.55 | Strongly Agree / Excellent |
| **Q5** | Document uploads, PDF downloads, and database transactions execute resourcefully with minimal server latency. *(Resource Utilization)* | 4.47 | 0.62 | Strongly Agree / Excellent |
| **Q6** | The platform effectively handles multi-user concurrent sessions and high-volume data retrieval across academic departments. *(Capacity)* | 4.50 | 0.67 | Strongly Agree / Excellent |
| **Q7** | The web system co-exists harmoniously with existing institutional IT infrastructure, operating systems, and client environments without system conflict. *(Co-existence)* | 4.53 | 0.62 | Strongly Agree / Excellent |
| **Q8** | The system exchanges data seamlessly with external cloud infrastructure, including Supabase PostgreSQL and S3-compatible cloud object storage. *(Interoperability)* | 4.60 | 0.55 | Strongly Agree / Excellent |
| **Q9** | Users can readily recognize and understand the purpose and status of each research phase through visual indicators and badges. *(Appropriateness Recognizability)* | 4.53 | 0.62 | Strongly Agree / Excellent |
| **Q10** | The platform is easy to learn and master for faculty researchers, review committees, and administrative staff without extensive training. *(Learnability)* | 4.53 | 0.62 | Strongly Agree / Excellent |
| **Q11** | The user interface provides straightforward controls, intuitive forms, and smooth navigation tailored to each stakeholder role. *(Operability)* | 4.40 | 0.66 | Strongly Agree / Excellent |
| **Q12** | The system protects users against operational errors through explicit validation warnings, confirmation dialogs, and field constraints. *(User Error Protection)* | 4.26 | 0.68 | Strongly Agree / Excellent |
| **Q13** | The design, visual hierarchy, color palette, typography, and card layouts deliver an aesthetically pleasing and professional experience. *(User Interface Aesthetics)* | 4.57 | 0.56 | Strongly Agree / Excellent |
| **Q14** | The platform maintains consistent operational stability under regular academic workload and prolonged user sessions. *(Maturity)* | 4.50 | 0.67 | Strongly Agree / Excellent |
| **Q15** | System services, proposal queues, and repository archives remain readily available and accessible whenever needed. *(Availability)* | 4.57 | 0.56 | Strongly Agree / Excellent |
| **Q16** | The system gracefully handles unexpected inputs or network fluctuations without causing unhandled exceptions or corrupting proposal data. *(Fault Tolerance)* | 4.23 | 0.67 | Strongly Agree / Excellent |
| **Q17** | Confidential research manuscripts, reviewer critiques, and evaluation scores are strictly protected against unauthorized disclosure. *(Confidentiality)* | 4.50 | 0.67 | Strongly Agree / Excellent |
| **Q18** | The platform preserves data integrity by preventing unauthorized tampering, role escalation, or circumvention of approval gates. *(Integrity)* | 4.53 | 0.62 | Strongly Agree / Excellent |
| **Q19** | All administrative actions, status updates, logins, and approvals are immutably logged with timestamps and actor identities. *(Accountability)* | 4.53 | 0.62 | Strongly Agree / Excellent |
| **Q20** | The application code is architected into clean, modular controllers, services, and Blade components that simplify maintenance. *(Modularity)* | 4.60 | 0.55 | Strongly Agree / Excellent |
| **Q21** | System logs, database schemas, and structured error messages facilitate straightforward issue diagnosis and analyzability. *(Analyzability)* | 4.57 | 0.56 | Strongly Agree / Excellent |
| **Q22** | Individual modules, API endpoints, and business logic pipelines can be effectively verified and unit-tested. *(Testability)* | 4.57 | 0.56 | Strongly Agree / Excellent |
| **Q23** | The web application dynamically adapts and renders legibly across diverse device form factors (desktop computers, laptops, tablets). *(Adaptability)* | 4.63 | 0.48 | Strongly Agree / Excellent |
| **Q24** | The system functions reliably across all standard modern web browsers, including Google Chrome, Microsoft Edge, Mozilla Firefox, and Safari. *(Browser Portability)* | 4.60 | 0.55 | Strongly Agree / Excellent |
| **Q25** | The application environment can be deployed, migrated, and configured reliably across staging and production cloud servers. *(Installability)* | 4.60 | 0.55 | Strongly Agree / Excellent |
| **GRAND MEAN** | **Overall System Quality Assessment Across All Indicators (Q1 – Q25)** | **4.52** | **0.60** | **Strongly Agree / Excellent** |

To provide a structured analytical perspective aligned with the international standard, the 25 indicator items were aggregated into their eight (8) respective ISO/IEC 25010 quality characteristics. Table 4 presents the characteristic summary, sub-means, average standard deviations, and verbal interpretations.

### Table 4. Summary of ISO/IEC 25010 Software Quality Evaluation by Characteristic
| Quality Characteristic | Associated Indicators | Characteristic Mean ($\bar{x}$) | Characteristic Average $SD$ | Verbal Interpretation | Ranking |
| :--- | :---: | :---: | :---: | :---: | :---: |
| **Portability** | Q23, Q24, Q25 | **4.61** | 0.53 | Strongly Agree / Excellent | 1 |
| **Maintainability** | Q20, Q21, Q22 | **4.58** | 0.56 | Strongly Agree / Excellent | 2 |
| **Compatibility** | Q7, Q8 | **4.57** | 0.59 | Strongly Agree / Excellent | 3 |
| **Functional Suitability** | Q1, Q2, Q3 | **4.53** | 0.60 | Strongly Agree / Excellent | 4 |
| **Security** | Q17, Q18, Q19 | **4.52** | 0.64 | Strongly Agree / Excellent | 5 |
| **Performance Efficiency**| Q4, Q5, Q6 | **4.52** | 0.61 | Strongly Agree / Excellent | 5 |
| **Usability** | Q9, Q10, Q11, Q12, Q13 | **4.46** | 0.63 | Strongly Agree / Excellent | 7 |
| **Reliability** | Q14, Q15, Q16 | **4.43** | 0.63 | Strongly Agree / Excellent | 8 |
| **OVERALL COMPOSITE** | **All 25 Quality Indicators** | **4.52** | **0.60** | **Strongly Agree / Excellent** | — |

```
========================================================================================
                 ISO/IEC 25010 Software Quality Evaluation - 30 Respondents
========================================================================================
  Portability            [███████████████████████████████████████] 4.61 - Excellent
  Maintainability        [██████████████████████████████████████░] 4.58 - Excellent
  Compatibility          [█████████████████████████████████████░░] 4.57 - Excellent
  Functional Suitability [████████████████████████████████████░░░] 4.53 - Excellent
  Security               [███████████████████████████████████░░░░] 4.52 - Excellent
  Performance Efficiency [███████████████████████████████████░░░░] 4.52 - Excellent
  Usability              [█████████████████████████████████░░░░░░] 4.46 - Excellent
  Reliability            [████████████████████████████████░░░░░░░] 4.43 - Excellent
----------------------------------------------------------------------------------------
  Grand Mean: 4.52 / 5.00  |  Average SD: 0.60  |  Interpretation: STRONGLY AGREE / EXCELLENT
========================================================================================
```

---

## Discussion

The empirical findings from both McCall’s Software Quality Model and the ISO/IEC 25010 Quality Evaluation demonstrate that the **University Research Lifecycle – Digital Platform (URLC System)** successfully satisfies both high-level software engineering benchmarks and end-user institutional requirements.

### 1. Analysis of Expert Evaluation Findings (McCall’s Model)
The IT Expert Evaluation yielded a **Grand Mean of 4.37** and an **Overall Quality Percentage of 87.67%**, confirming that the platform adheres to rigorous technical standards.

* **Architectural Modularity & Consistency:** The experts highlighted the platform's robust adoption of the Model-View-Controller (MVC) architectural pattern in **Laravel 8 (PHP 8)**. By decoupling business logic (dedicated controllers for Researchers, Reviewers, Deans, Budget Officers, and Administrators), data persistence (Eloquent ORM interacting with PostgreSQL on Supabase Cloud), and presentation (Laravel Blade templating engine with Tailwind CSS and Alpine.js), the platform achieves superior code maintainability and testability. Expert 1 specifically commended the normalization of the database schema into 16 relational tables (Third Normal Form, 3NF), which prevents data anomalies and enforces referential integrity across multi-tiered institutional sign-offs.
* **Security & Administrative Accountability:** The experts affirmed the efficacy of the customized Laravel middleware pipeline (`RoleMiddleware`, `ApprovalMiddleware`, and `PreventBackHistory`). This pipeline strictly enforces Role-Based Access Control (RBAC) across the platform’s nine (9) specialized roles, preventing privilege escalation and unauthorized route access. Furthermore, the integration of **Spatie ActivityLog** was recognized as a vital engineering feature, as it maintains an immutable audit trail capturing user IDs, timestamps, IP addresses, and state changes (before-and-after values) for all critical actions (approvals, revisions, budget certifications, and document submissions).
* **Document Generation & Asynchronous Queues:** The expert panel praised the server-side document generation engine powered by **Laravel DomPDF**, which compiles Blade views into publication-ready, standardized PDF instruments—specifically the **Notice of Acceptance (NOA)**, **Notice to Proceed (NTP)**, and **Certificate of Research Completion**—complete with dynamic verification timestamps and institutional headers. The utilization of background queue workers for dispatching transactional email notifications via SMTP ensures that resource-intensive operations do not hinder frontend responsiveness.
* **Areas for Future Enhancement Identified by Experts:** While Expert 1 (4.79 / 96%) and Expert 2 (4.68 / 94%) evaluated the system as exceptionally mature, Expert 3 (3.63 / 73%) offered constructive critiques that illuminate valuable pathways for post-capstone expansion. Specifically, Expert 3 emphasized that while the platform successfully manages PDF uploads via Supabase S3-compatible cloud storage, the absence of an integrated automated plagiarism checking engine (such as direct Turnitin API hooks) requires administrators to conduct similarity checks manually outside the system. Additionally, Expert 3 noted that offline data caching and synchronization are currently unavailable due to the strictly web-based architecture. As documented in the study’s delimitations in Chapter 1, these features were intentionally scoped out to prioritize institutional workflow governance and cloud security; nonetheless, they represent high-value opportunities for future development iterations.

### 2. Analysis of End-User Evaluation Findings (ISO/IEC 25010)
The end-user evaluation conducted with thirty (30) stakeholders across the University of Antique yielded a **Grand Mean of 4.52 (SD = 0.60)**, interpreted as **"Strongly Agree" / "Excellent"**. All twenty-five individual indicators obtained mean scores well above the 4.21 threshold, underscoring high stakeholder satisfaction and strong institutional acceptability.

* **Portability ($\bar{x} = 4.61$, Ranked 1st):** Portability achieved the highest characteristic rating. Evaluators strongly agreed that the web interface is exceptionally responsive across various screen resolutions and devices (Q23: $\bar{x} = 4.63, SD = 0.48$), operating smoothly on desktop workstations, laptops, and tablets. Browser compatibility (Q24: $\bar{x} = 4.60, SD = 0.55$) and cloud deployment consistency (Q25: $\bar{x} = 4.60, SD = 0.55$) confirmed that users experienced no rendering anomalies across Google Chrome, Microsoft Edge, Mozilla Firefox, or Safari. This was achieved through the utility-first responsiveness of Tailwind CSS and modern CSS flexbox/grid containers.
* **Maintainability ($\bar{x} = 4.58$, Ranked 2nd):** Stakeholders and technical coordinators recognized the clear modularity of the system (Q20: $\bar{x} = 4.60, SD = 0.55$), its transparent analyzability through structured logging and intuitive error reporting (Q21: $\bar{x} = 4.57, SD = 0.56$), and verifiable testability (Q22: $\bar{x} = 4.57, SD = 0.56$). Database migrations and seeders allow the system to be cleanly maintained and deployed with zero data loss.
* **Compatibility ($\bar{x} = 4.57$, Ranked 3rd):** The URLC platform demonstrated excellent co-existence with existing campus IT infrastructure (Q7: $\bar{x} = 4.53, SD = 0.62$) and seamless cloud interoperability (Q8: $\bar{x} = 4.60, SD = 0.55$). The use of Flysystem S3 drivers enables frictionless communication with Supabase cloud object storage, streaming documents via time-signed, secure URLs without congesting local server storage.
* **Functional Suitability ($\bar{x} = 4.53$, Ranked 4th):** The platform achieved outstanding ratings for functional completeness (Q1: $\bar{x} = 4.60, SD = 0.55$), functional appropriateness (Q3: $\bar{x} = 4.53, SD = 0.62$), and functional correctness (Q2: $\bar{x} = 4.47, SD = 0.62$). Stakeholders verified that the system comprehensively automates the five-phase lifecycle: (1) Proposal Submission & College Intake, (2) Technical & Ethics Evaluation, (3) College Dean Noting & Budget Certification, (4) Director Endorsement & VPREI Executive Approval, and (5) Milestone Monitoring & Archiving. The ability to dynamically generate official Notice to Proceed (NTP) forms directly upon executive approval eliminated weeks of manual administrative routing.
* **Security ($\bar{x} = 4.52$, Ranked 5th):** Confidentiality (Q17: $\bar{x} = 4.50, SD = 0.67$), integrity (Q18: $\bar{x} = 4.53, SD = 0.62$), and accountability (Q19: $\bar{x} = 4.53, SD = 0.62$) all received top marks. Users expressed high confidence in the platform's ability to protect sensitive intellectual property, enforce role boundaries, prevent unauthorized document alterations, and maintain transparent audit trails of all institutional approvals.
* **Performance Efficiency ($\bar{x} = 4.52$, Ranked 5th):** Users commended the fast time behavior (Q4: $\bar{x} = 4.60, SD = 0.55$), efficient resource utilization (Q5: $\bar{x} = 4.47, SD = 0.62$), and concurrent capacity (Q6: $\bar{x} = 4.50, SD = 0.67$). Pages rendered in under two seconds due to server-side Blade caching, optimized Eloquent query indexing, and asynchronous offloading of email notifications to background queue workers.
* **Usability ($\bar{x} = 4.46$, Ranked 7th):** The presentation layer, enriched with Alpine.js and Axios, received high praise for user interface aesthetics (Q13: $\bar{x} = 4.57, SD = 0.56$), appropriateness recognizability (Q9: $\bar{x} = 4.53, SD = 0.62$), and learnability (Q10: $\bar{x} = 4.53, SD = 0.62$). Operability received a solid rating of 4.40 ($SD = 0.66$). User error protection scored 4.26 ($SD = 0.68$), representing a positive score within the "Excellent" range while suggesting that form validation feedback could be made even more visible to novice users during multi-step budget encoding.
* **Reliability ($\bar{x} = 4.43$, Ranked 8th):** System availability scored 4.57 ($SD = 0.56$) and operational maturity scored 4.50 ($SD = 0.67$). Fault tolerance received a rating of 4.23 ($SD = 0.67$), indicating that while the system reliably prevents database corruption via ACID-compliant PostgreSQL transactions, future versions should implement client-side network reconnection dialogs and offline draft persistence when university internet connections experience sudden dropouts.

### 3. Synthesis and Institutional Impact
Prior to the development of the URLC System, the University of Antique Research Unit managed proposal submissions and tracking through disparate spreadsheets, manual Google Drive folders, and physical paper routing. This legacy process suffered from misplaced files, untracked review turnaround times, bottlenecks during budget validation, and lack of institutional visibility.

The implementation and empirical validation of the URLC Platform directly resolves these institutional pain points. By replacing fragmented manual handling with an automated, role-governed digital pipeline, the system establishes a single source of truth for university research. The high evaluation marks across both technical experts (4.37 / 87.67%) and university stakeholders (4.52 / Excellent) confirm that the platform is technically sound, administratively robust, and fully ready for institutional adoption.

---

# CHAPTER V
# SUMMARY, CONCLUSION AND RECOMMENDATIONS

## Summary

The development of the **University Research Lifecycle – Digital Platform (URLC System)** originated from a recognized institutional imperative at the **University of Antique**: the urgent need to modernize, automate, and centralize the university's research management operations. Under the supervision of the Research Unit, institutional research involves a rigorous sequence of activities—ranging from call-for-papers dissemination, proposal intake, college screening, and technical peer review to line-item budget verification, executive approval, implementation monitoring, and public repository archiving. Historically, these processes were handled through a fragmented combination of physical paper submissions, Microsoft Excel tracking spreadsheets, personal Google Drive folders, and uncoordinated email communications. This manual paradigm routinely resulted in prolonged approval delays, misplaced manuscripts, data duplication, lack of transparency in reviewer feedback, and difficulties in monitoring post-award research milestones.

To address these operational bottlenecks, the capstone research team—comprising **Andrea Marie M. Jaromay, Hanna Mae P. Domingo, Lawrence S. Sadje, and Michelle F. De Los Santos**, candidates for the degree of Bachelor of Science in Information Technology at the College of Computing and Information Sciences (CCIS)—undertook the development and formal evaluation of the URLC System.

Following the **Agile Software Development Life Cycle (SDLC)** methodology, the project proceeded through iterative, two-week development sprints structured across seven Level-1 work breakdown packages: Project Management, Requirements Engineering & Analysis, System Architecture & Design, Software Development & Implementation, Testing & Quality Assurance, Deployment & Configuration, and Project Closure & Handover. 

During the initial requirements elicitation phase, the team engaged key university stakeholders to formulate a comprehensive Software Requirements Specification (SRS). A pivotal design refinement emerged during early consultations and the Pre-Oral Defense, where academic advisers and panel members emphasized the necessity of incorporating a comprehensive **Institutional Budget Allocation Matrix** capable of tracking funds across four funding streams (GAA, Local Trust Fund, Foreign Trust Fund, and Implementation Trust Fund) across the Main Campus (spanning 75 academic program units across 8 colleges and the Graduate School) as well as the four external satellite campuses (Libertad, Caluya, Hamtic, and Tibiao). Furthermore, the panel recommended integrating dynamic, publication-grade generation of official legal instruments, specifically the **Notice of Acceptance (NOA)**, **Notice to Proceed (NTP)**, and **Certificate of Research Completion**, directly into the platform to replace manual paper signing.

The software architecture was engineered upon a modern, decoupled five-tier web framework:
1. **Presentation Tier:** Built using the Laravel Blade templating engine, styled with **Tailwind CSS** and **Bootstrap 5.3**, and enriched with **Alpine.js** and **Axios** to deliver responsive, asynchronous user experiences without disruptive full-page reloads.
2. **Application Tier:** Powered by **Laravel 8** running on **PHP 8**, executing core business logic through modular controllers organized around nine (9) specialized institutional roles: Faculty Researchers, College Coordinators, College Deans, Support Staff (Receiving), Support Staff (Recording), Technical Reviewers/Evaluators, Budget Officers, Research Administrators (Research Director), and the Vice President for Research, Extension, and Innovation (VPREI), overseen by a pre-seeded Super Admin.
3. **Security Gate & Middleware Tier:** Enforcing Bcrypt password hashing, Cross-Site Request Forgery (CSRF) protection, multi-role session verification, account registration approval guards, and strict Role-Based Access Control (RBAC).
4. **Cloud Persistence Tier:** Utilizing **PostgreSQL 15+ hosted on Supabase Cloud** for relational data persistence (normalized to 3NF across 16 tables) and **Supabase S3-Compatible Cloud Object Storage** (interfaced via the Flysystem S3 driver) for secure manuscript and document handling using expiring time-signed URLs.
5. **Background & Document Engine:** Leveraging Laravel Queue Jobs for asynchronous email notification dispatching via Gmail SMTP, **Laravel DomPDF** for automated legal notice generation, and **Spatie ActivityLog** for immutable event audit logging.

Throughout the development process, the team surmounted significant engineering hurdles, including orchestrating multi-role session toggling without privilege leakage, configuring time-signed cloud asset streaming, optimizing database queries to eliminate N+1 bottlenecks, and coordinating development sprints under demanding academic schedules. 

Upon prototype completion, the platform underwent comprehensive multi-tier testing, including unit testing of models and controllers, RBAC boundary verification, and end-to-end simulation of the five-phase lifecycle. Formal software quality evaluation was executed using two rigorous frameworks:
* **McCall’s Software Quality Model:** Administered to three (3) IT Experts from the College of Computing and Information Sciences, University of Antique, yielding an overall score of **249 out of 285**, a **Grand Mean of 4.37**, and an **Average Percentage Quality of 87.67%**, corresponding to an **"Excellent"** quality rating.
* **ISO/IEC 25010 Software Product Quality Model:** Administered to thirty (30) purposively sampled institutional stakeholders across the university, yielding an overall **Grand Mean of 4.52 (SD = 0.60)** across 25 quality indicators, interpreted as **"Strongly Agree" / "Excellent"**.

The resulting URLC System represents a fully functional, cloud-enabled, workflow-oriented digital platform that centralizes research administration, enforces accountability, eliminates administrative friction, and elevates the research culture of the University of Antique.

---

## Conclusion

Based on the development process, empirical testing, and formal software quality evaluation of the **University Research Lifecycle – Digital Platform (URLC System)**, the following conclusions are drawn in direct alignment with the study's general and specific objectives:

1. **Successful Realization of a Centralized Research Lifecycle Platform (General Objective):** The study successfully designed, developed, and deployed the URLC System as a centralized, web-based platform that replaces fragmented spreadsheets and manual paper handling with an integrated digital ecosystem. The platform seamlessly governs the entire university research lifecycle from pre-award proposal submission and multi-stage evaluation to budget certification, executive approval, milestone monitoring, and repository archiving.
2. **Robust Multi-Role Governance via RBAC (Specific Objective 1):** The platform successfully implemented an enterprise-grade Role-Based Access Control (RBAC) architecture. Through customized Laravel middleware (`RoleMiddleware`, `ApprovalMiddleware`, `PreventBackHistory`), the system provides distinct, role-tailored dashboards and secure operational queues for all nine (9) institutional roles: Faculty Researchers, College Coordinators, College Deans, Support Staff (Receiving), Support Staff (Recording), Technical Reviewers/Evaluators, Budget Officers, Research Administrators (Research Director), and the VPREI, with top-level system governance maintained by the Super Admin.
3. **Structured Sequential Monitoring Pipeline (Specific Objective 2):** The system established a transparent, five-phase monitoring pipeline that guides research submissions through every institutional checkpoint. Researchers and administrators possess real-time visibility into the exact location, reviewing stage, and compliance status of any given proposal, eliminating the ambiguity and lost files that plagued legacy manual practices.
4. **Automated Workflow and Status State Management (Specific Objective 3):** The platform successfully automated proposal workflow transitions. Proposals dynamically progress through verified states (*Draft, Submitted, Pending Coordinator Endorsement, Endorsed, Received, Under In-House Review, Revision Required, Approved by Reviewers, Final Copy Noted, Funds Certified, Endorsed to VPREI, Final Approved, Ongoing, and Completed*) based on authenticated user actions and rubric evaluations.
5. **Interactive Community Engagement and Announcement Module (Specific Objective 4):** The platform deployed an integrated Announcement Module enabling administrators to publish institutional Call for Papers, In-House Review schedules, and Inception meeting notices. Interactive features, such as user comments and likes, foster collaborative engagement and transparent communication across the university's academic community.
6. **Streamlined Document Processing & Automated Legal Notice Generation (Specific Objective 5):** The system established secure, decoupled cloud document management using Supabase S3-compatible object storage, safeguarding research manuscripts and attachments via encrypted, time-signed URLs. Furthermore, the integration of Laravel DomPDF automated the dynamic generation of official institutional legal notices—specifically the **Notice of Acceptance (NOA)**, **Notice to Proceed (NTP)**, and **Certificate of Research Completion**—complete with authorized digital signatures, institutional seals, and immutable verification timestamps.
7. **Exceptional Empirical Software Quality (Specific Objective 6):** The empirical evaluation confirmed that the URLC System meets and exceeds recognized international software engineering standards. The system achieved a **Grand Mean of 4.37 (87.67% Quality Rating, "Excellent")** under McCall’s Software Quality Model from IT Experts, and a **Grand Mean of 4.52 (SD = 0.60, "Strongly Agree" / "Excellent")** across all eight quality characteristics of the ISO/IEC 25010 model from thirty institutional end-users. Evaluators particularly commended the system's cross-device Portability (4.61), architectural Maintainability (4.58), cloud Compatibility (4.57), and Functional Suitability (4.53).

In conclusion, the URLC System proves that modern, open-source web technologies (Laravel, PostgreSQL, Supabase Cloud, Tailwind CSS, Alpine.js) can deliver a cost-effective, highly reliable, and institutionally customized alternative to commercial, off-the-shelf research management software. The platform provides the University of Antique with a powerful digital asset that enhances administrative efficiency, strengthens research governance, preserves institutional knowledge, and accelerates institutional digital transformation.

---

## Recommendations

Based on the empirical findings, expert evaluations, and operational experiences gained during the development and testing of the URLC System, the following recommendations are proposed for future system iterations, institutional policy adoption, and subsequent capstone endeavors:

### 1. Functional and Architectural Enhancements
* **Integration of Automated Plagiarism and Similarity Detection Engines:** While the platform currently provides secure storage for PDF manuscripts, similarity verification is conducted manually outside the system. Future developers should integrate automated plagiarism detection APIs (such as Turnitin, CopyLeaks, or Unicheck) directly into the proposal intake pipeline, allowing automatic similarity index generation and highlighted match reporting upon initial manuscript upload.
* **National Research Portal Synchronization (DOST & CHED APIs):** To reduce redundant data entry for faculty researchers applying for external grants, future iterations should develop API bridges that synchronize proposal metadata with national repositories, such as the Department of Science and Technology – National Integrated Basic Research Information System (DOST-DPMIS) and the Commission on Higher Education (CHED) Research and Extension Database.
* **Digital Signatures and Cryptographic Verification (PKI):** Although the platform compiles official PDF documents with dynamic timestamps and graphical signatures, future enhancements should incorporate cryptographic Public Key Infrastructure (PKI) digital signatures (such as Philippine National PKI - PNPKI) to provide legally binding, tamper-evident non-repudiation on all generated Notices to Proceed and Certificates of Completion.

### 2. User Experience and Accessibility
* **Progressive Web App (PWA) and Offline Data Caching:** To accommodate faculty researchers and evaluators working in areas with intermittent internet connectivity, the frontend should be upgraded into a Progressive Web App (PWA) with Service Workers and IndexedDB client storage. This will enable reviewers to read manuscripts and draft offline evaluation comments that automatically synchronize once internet connectivity is restored.
* **Native Mobile Application Development:** While the current responsive web design achieved an excellent portability rating ($\bar{x} = 4.61$), developing dedicated companion mobile applications (using Flutter or React Native) for Android and iOS would enable native push notifications, biometrics login (fingerprint/Face ID), and rapid milestone tracking on personal smartphones.
* **Multi-Factor Authentication (MFA) and Advanced Security:** To further enhance data protection, future deployments should mandate Multi-Factor Authentication (MFA) via Time-Based One-Time Password (TOTP) authenticator apps (e.g., Google Authenticator) or email OTP codes for administrative, financial, and executive user accounts.

### 3. Institutional Analytics and Business Intelligence
* **Interactive Executive Analytics and KPI Dashboards:** While the VPREI and Research Director dashboards provide vital proposal counters, integrating rich Business Intelligence (BI) visualizations (such as Chart.js or Apache ECharts) would allow executive leaders to track longitudinal research trends, college citation metrics, fund disbursement velocity, and faculty productivity heatmaps across fiscal years.
* **Automated Institutional Research Compendiums:** The repository module should be expanded to include automated PDF compilation of annual University Research Compendiums and abstracts, allowing the Research Unit to generate publication-ready annual reports for CHED and SUC levelling audits with a single click.

### 4. Institutional Adoption and Policy Recommendations
* **Formal Policy Adoption and Procedural Transition:** The administration of the University of Antique, through the Office of the VPREI and the Board of Regents, is strongly encouraged to issue a formal University Memorandum designating the URLC System as the official, sole repository and processing gateway for all institutional research proposals, thereby formally retiring legacy manual spreadsheets.
* **Comprehensive University-Wide Training and Capacity Building:** The Research Unit, in collaboration with the College of Computing and Information Sciences, should conduct structured orientation and hands-on training workshops for faculty researchers, college deans, department coordinators, and administrative staff across the Main Campus and all four satellite campuses (Libertad, Caluya, Hamtic, and Tibiao) to ensure smooth institutional adoption and change management.
* **Dedicated Cloud Hosting and Maintenance SLA:** The university IT Department should establish dedicated, production-grade cloud server hosting with automated daily database backups, SSL certificate auto-renewals, and formal Service Level Agreements (SLA) to guarantee 99.9% platform availability and long-term sustainability.
