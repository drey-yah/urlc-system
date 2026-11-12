# CHAPTER III – DESIGN AND METHODOLOGY
## REVISED SECTIONS & ACADEMIC DESCRIPTIONS
**Aligned with the Capstone Manuscript Guide (Prof. Sarah Mae R. Silva)**

---

## 1. Functional Requirements Specification

Per the revised Chapter III guidelines (Page 2), functional requirements must be formally itemized with unique identifiers, requirement descriptions, priority levels, and verifiable acceptance indicators.

### Table 3. Functional Requirements of the URLC System
| ID | Functional Requirement | Priority | Acceptance Indicator |
|:---:|:---|:---:|:---|
| **FR-01** | The system shall authenticate authorized users and enforce Role-Based Access Control (RBAC) across nine specialized roles and a pre-seeded Super Admin. | High | Valid credentials grant access strictly to assigned role dashboards; unauthorized route attempts return HTTP 403 Forbidden. |
| **FR-02** | The system shall require Super Admin approval for newly registered administrative, staff, and evaluator accounts prior to login activation. | High | Unapproved accounts cannot authenticate; status updates to 'Active' upon administrator confirmation. |
| **FR-03** | The system shall enable faculty researchers to encode proposal metadata, construct itemized Line-Item Budgets (LIB: PS, MOOE, CO), and upload PDF manuscripts. | High | Proposal records are saved to PostgreSQL; manuscript files are stored securely in Supabase S3 cloud storage. |
| **FR-04** | The system shall route submitted proposals to the designated College Coordinator for departmental screening and endorsement. | High | Proposals appear filtered in the coordinator's college queue; coordinator can endorse or return with revision remarks. |
| **FR-05** | The system shall allow Support Staff (Receiving) to perform documentary completeness checks on endorsed proposals. | High | Staff can verify required attachments and forward verified dossiers to the Research Director. |
| **FR-06** | The system shall enable the Research Director to assign qualified Technical Reviewers to verified proposals. | High | Assigned proposals populate the designated reviewer’s queue; email notification is triggered. |
| **FR-07** | The system shall provide reviewers with an interactive Evaluation Rubric to record quantitative scores and structured qualitative feedback. | High | Rubric scores, comments, and recommendations (Approve, Revision Required, Reject) are immutably stored and displayed to the researcher. |
| **FR-08** | The system shall manage version-controlled resubmissions when reviewers request manuscript revisions. | High | Researchers can upload revised drafts (`ProposalRevision`); previous versions remain accessible in the audit history. |
| **FR-09** | The system shall allow the College Dean to note final revised manuscripts and the Budget Officer to certify fund availability. | High | Dean noting updates proposal status; Budget Officer audits line items against fund ceilings and sets status to `funds_certified`. |
| **FR-10** | The system shall enable the Research Director to endorse cleared dossiers to the VPREI for final executive approval. | High | Executive queue displays full dossier history; VPREI can execute binding executive approval or disapproval. |
| **FR-11** | The system shall automatically compile and generate standardized, publication-grade PDF legal instruments (NOA, NTP, Certificate of Completion) via DomPDF. | High | Upon VPREI approval, styled PDF documents with institutional headers and dynamic data are generated and made downloadable. |
| **FR-12** | The system shall provide an Institutional Budget Allocation Matrix tracking funds (GAA, Local Trust, Foreign Trust, Implementation Trust) across 75 academic programs and 4 satellite campuses. | Medium | Budget allocations compute live program subtotals and institutional grand totals with print-ready summary export. |
| **FR-13** | The system shall provide an interactive Announcement Module for publishing Calls for Papers and review schedules with likes and comments. | Medium | Announcements display banner images; users can post comments and toggle likes with real-time counter updates. |
| **FR-14** | The system shall record critical user actions, state changes, and timestamps into an immutable audit trail using Spatie ActivityLog. | High | Audit table records user ID, IP address, event type, and before-and-after change sets viewable by administrators. |

---

## 2. Non-Functional Requirements Specification (ISO/IEC 25010:2023 Quality Model)

### Table 4. Non-Functional Requirements
| Quality Characteristic | Requirement Specification | Verifiable Metric / Standard |
|:---|:---|:---|
| **Functional Suitability** | Fully covers all five phases of the university research lifecycle as defined by University of Antique research policy. | 100% of required institutional checkpoints implemented. |
| **Performance Efficiency** | Page rendering and database query retrieval shall execute in under 2 seconds under standard campus network conditions. | Sub-2.0s server response time via server-side Blade caching and indexed queries. |
| **Compatibility** | Operates uniformly across major modern web browsers and seamlessly exchanges data with Supabase S3 storage. | 100% functional consistency across Google Chrome, Microsoft Edge, Firefox, and Safari. |
| **Usability** | Interface features intuitive navigation, responsive cards, and minimal cognitive load for faculty and staff. | Evaluated with a target usability mean rating $\ge 4.20$ on a 5-point Likert scale. |
| **Reliability & Fault Tolerance** | Database transactions must enforce ACID compliance to prevent orphaned records or partial writes during multi-tier routing. | Zero orphaned records; automatic rollback on query failures. |
| **Security** | Enforces HTTPS encryption, CSRF protection, Bcrypt password hashing, PDO parameterized queries, and role-scoped middleware. | Zero privilege escalation vulnerabilities; all routes guarded by `RoleMiddleware`. |
| **Maintainability** | Decoupled Model-View-Controller architecture with standardized Eloquent migrations and modular service providers. | Codebase adheres to PSR-12 coding standards. |
| **Portability** | Responsive frontend layouts adapt dynamically to desktop, laptop, and tablet viewports via Tailwind CSS. | Responsive viewports ranging from 768px (tablets) to 1920px (desktops). |

---

## 3. 1-2 Paragraph Academic Descriptions for Modeling Diagrams

Per the guideline (Pages 10–19), every diagram must be accompanied by a formal **1-2 paragraph description** explaining the components, data flows, and operational significance.

### Figure 8. Functional Decomposition Diagram (FDD)
Figure 8 illustrates the hierarchical functional decomposition of the University Research Lifecycle – Digital Platform (URLC System). The top-level root represents the entire platform, which decomposes into seven primary functional domains: User and Access Governance, Proposal Intake and Routing, Review and Evaluation, Budget and Financial Certification, Approvals and Document Generation Engine, Milestone and Repository Management, and Communication and System Administration. Each core domain further branches into granular sub-functions, illustrating how complex institutional responsibilities are partitioned into manageable, modular subsystems.

This decomposition establishes clear functional boundaries across the software engineering lifecycle. By isolating responsibilities—such as separating Line-Item Budget validation from peer review scoring—the diagram provides a clear architectural blueprint for developers. It ensures that every operational requirement identified during stakeholder interviews maps directly to a discrete set of backend controllers and user interface components.

### Figure 9. System Context Diagram
Figure 9 depicts the System Context Diagram (Level 0 DFD) of the URLC platform, establishing the system's operational boundary and defining its external interactions. At the core is the URLC Digital Platform, which functions as the central transactional and workflow processing hub. Surrounding the platform are nine distinct external actors representing the institutional stakeholders (Faculty Researcher, College Coordinator, College Dean, Support Staff Receiving, Support Staff Recording, Technical Reviewer, Budget Officer, Research Director, and VPREI) along with the Supabase Cloud Infrastructure (PostgreSQL and S3 Storage) as an external data persistence entity.

Directed arrows illustrate the primary bidirectional information flows between external entities and the system core. For example, Faculty Researchers input proposal metadata and PDF manuscripts and receive real-time status alerts and generated legal notices. Evaluators receive assigned manuscripts and submit structured rubric scores. The cloud infrastructure exchanges relational queries and streams secure, time-signed document links. The diagram confirms that all stakeholder interactions are mediated through secure system interfaces, ensuring data integrity across the university.

### Figure 10. System Flowchart (Swimlanes)
Figure 10 presents the System Flowchart organized into color-coded swimlanes representing the nine institutional roles and the underlying technical system core. The flowchart details the sequential operational logic, decision diamonds, automated status transitions, and document exchanges spanning all five formal phases of the university research lifecycle. Beginning in the Researcher lane with proposal encoding and Line-Item Budget specification, the flow advances through sequential gatekeeping checkpoints in the Coordinator, Receiving Staff, Reviewer, Dean, Budget Officer, Director, and VPREI lanes.

The swimlane structure emphasizes the procedural rigor and automated handoffs embedded within the platform. If a proposal is deemed deficient at any stage—such as during coordinator screening or technical peer review—decision diamonds route the submission back into the Researcher lane with revision remarks, preventing further progression until compliance is re-verified. Once the VPREI lane executes final executive approval, the System Core automatically triggers the DomPDF engine to render the official Notice of Acceptance and Notice to Proceed, transitioning the project into Phase 5 implementation and repository archiving.

### Figure 26. Entity Relationship Diagram (ERD)
Figure 26 presents the logical database schema and relational architecture of the URLC System, normalized up to the Third Normal Form (3NF) to eliminate data redundancy and ensure referential integrity. The schema comprises sixteen primary relational tables organized into four core functional domains: User Identity and Access (`users`, `user_roles`, `proposal_collaborators`), Proposal Lifecycle and Revisions (`research_proposals`, `proposal_documents`, `proposal_revisions`, `proposal_assignments`, `proposal_reviews`), Institutional Budgeting (`proposal_budget_items`, `budget_allocations`), and Implementation & Governance (`research_milestones`, `activity_designs`, `purchase_requests`, `terminal_reports`, `announcements`, `announcement_interactions`, `activity_log`, `system_settings`).

Foreign key constraints and cascading behaviors guarantee transactional consistency across complex multi-phase workflows. The central `research_proposals` table serves as the primary relational anchor, linking author identities, line-item budget records, reviewer rubric scores, revision histories, and generated document paths. This structure supports high-concurrency relational queries on Supabase PostgreSQL while decoupling heavy binary files to cloud object storage.

---

## 4. 1-2 Paragraph Academic Descriptions for UI Screenshots (Figures 31–86)

Per the guideline (Pages 19–20), the existing brief one-liners in the manuscript must be replaced with structured 1-2 paragraph academic descriptions detailing: (1) purpose and intended users, (2) layout, controls, and workflows shown, and (3) observed behavior and outputs. Below are the revised descriptions for the key operational figures:

### Figure 31. User Register and Login Form
Figure 31 shows the authentication interface of the URLC platform, designed for all institutional users. The interface provides dual functionality: an account registration tab where new users submit their full name, institutional email, password, assigned campus, and college affiliation; and a secure login portal featuring input validation, "Remember Me" session persistence, and password visibility toggling. The layout utilizes responsive styling with visual institutional branding.

Upon credential submission, the form sends an encrypted HTTPS POST request protected by a CSRF token. The backend validates credentials using Bcrypt hashing, verifies whether administrative accounts have received Super Admin approval, and routes the authenticated user to their role-tailored dashboard. Invalid login attempts trigger immediate client-side error highlights stating that credentials do not match system records, preventing unauthorized access.

### Figure 32. Faculty Researcher's Dashboard
Figure 32 illustrates the Faculty Researcher Dashboard, serving as the central workspace for university faculty members. The upper section features four analytical KPI metric cards displaying real-time counters for Total Submissions, Proposals Under Review, Revisions Needed, and Approved Researches. Below the metrics, an active proposals table lists recent submissions, showing proposal tracking codes, titles, assigned categories, and dynamic lifecycle status badges.

The dashboard provides quick-action shortcuts allowing researchers to initiate new submissions, access the public research repository, or view recent institutional announcements. When a proposal requires author revision, the system displays an alert banner directing the researcher to the reviewer critique workspace. The interface provides faculty members with complete visibility over their submissions without requiring in-person visits to the Research Unit.

### Figure 33. Researcher Researches Module
Figure 33 depicts the Researcher Researches Module, which provides comprehensive tracking of all proposals submitted by the logged-in faculty member. The interface incorporates multi-criteria filtering options, allowing researchers to sort proposals by lifecycle phase (Phases 1 through 5), approval status (e.g., *Draft*, *Under Review*, *Revision Required*, *Approved*), or fiscal year. Each record row features a visual progress bar indicating the exact checkpoint reached in the approval pipeline.

From this view, researchers can access detailed proposal dossiers, download previous manuscript revisions, or view evaluator feedback. The interface dynamically disables action buttons for proposals undergoing active evaluation while enabling revision submission buttons when updates are officially requested, preventing conflicting concurrent edits.

### Figure 34. Researcher Submission Module
Figure 34 displays the Researcher Submission Module, which facilitates the intake of new research proposals. The form guides the researcher through structured input sections: Basic Information (Title, Abstract, Research Field, Rationale, and Agenda Alignment), Collaborator Management (adding co-authors from registered faculty), dynamic Line-Item Budgeting (PS, MOOE, CO breakdown), and Manuscript Attachment via a drag-and-drop file upload zone.

The module implements real-time client-side validation to ensure that all mandatory fields are completed before submission. The file upload handler restricts file formats strictly to PDF and DOCX documents with a maximum file size limit of 20 MB. Upon clicking "Submit Proposal," the system stores the proposal metadata in PostgreSQL, streams the PDF to Supabase S3 storage, assigns a unique tracking code (e.g., `RP-2026-002`), and transitions the proposal to Phase 1.

### Figure 35. Researcher Proposal Details Module
Figure 35 illustrates the Researcher Proposal Details Module, providing an in-depth view of a specific research proposal dossier. The top of the interface features an interactive five-phase progression timeline highlighting the current operational status. The main body displays the complete proposal abstract, itemized Line-Item Budget, attached manuscript links, and a dedicated Reviewer Feedback panel.

When technical reviewers or coordinators request manuscript adjustments, this module displays evaluator rubric scores alongside line-by-line comments and suggestions. A version-controlled "Submit Revision" button allows the researcher to attach an updated manuscript, which automatically creates a new `ProposalRevision` record while preserving the historical draft for administrative auditing.

### Figure 40. College Coordinator's Dashboard
Figure 40 depicts the College Coordinator Dashboard, serving as the first-line quality-control portal for collegiate research. The interface aggregates departmental research metrics and displays an "Endorsement Center" queue containing newly submitted proposals originating exclusively from the coordinator's academic college. 

Coordinators can inspect proposal abstracts, verify compliance with departmental priorities, and review attached manuscripts. Action buttons enable the coordinator to execute "Endorse Proposal," which forwards the dossier to Support Staff (Receiving), or "Return for Revision," which opens a modal dialog requiring specific correction remarks that are dispatched immediately to the researcher.

### Figure 46. College Dean's Dashboard
Figure 46 shows the College Dean Dashboard, designed for collegiate executive oversight. The dashboard presents college-wide research metrics, including total submissions, active funded grants, and faculty involvement rates across departments. Below the metrics, two operational queues partition proposals into Phase 1 Initial Endorsement Noting and Phase 3 Post-Evaluation Final Copy Noting.

Selecting a proposal allows the Dean to review the complete evaluation history, including reviewer rubric scores and verified manuscript revisions. The Dean executes "Note Final Copy," digitally recording collegiate concurrence and automatically transferring the proposal to the Budget Officer's financial certification queue.

### Figure 50. Support Staff (Receiving) Dashboard
Figure 50 illustrates the Support Staff (Receiving) Dashboard, functioning as the centralized intake verification queue at the University Research Unit. The interface lists all coordinator-endorsed proposals awaiting preliminary document checking. 

Staff members utilize an integrated compliance checklist to verify documentary completeness, checking for ethics clearance forms, endorsement letters, and formatting compliance. Once verified, staff execute "Receive & Forward," which logs the official intake timestamp (`received_at`) and advances the proposal to the Research Director for reviewer assignment.

### Figure 56. Reviewer's Dashboard and Review Feedbacking
Figure 56 presents the Technical Reviewer Dashboard, supporting the peer review and ethics evaluation workflow. The interface lists all research manuscripts assigned to the logged-in evaluator by the Research Director. Evaluators can view proposal titles, submission dates, and download manuscripts via secure time-signed URLs.

Clicking "Review" opens the evaluation workspace, where the reviewer completes a standardized scoring rubric covering technical merit, methodology, ethical considerations, and institutional relevance. The reviewer inputs qualitative comments and recommendations and submits a binding decision (*Approve*, *Revision Required*, or *Reject*), which updates the proposal state and notifies the research team.

### Figure 62. Support Staff (Recording) Dashboard & Routing Tracker
Figure 62 depicts the Support Staff (Recording) Dashboard, providing institutional oversight across all research projects. The primary feature is the Active Proposals Routing Tracker, which displays the real-time status, assigned evaluator, current phase, and operational location of every proposal in the university.

Recording staff can apply multi-criteria filters to generate progress reports, track overdue evaluations, and monitor project milestones. The interface also manages the archiving of completed research projects, enabling staff to upload verified terminal reports and catalog final manuscripts into the university's public document repository.

### Figure 63. Institutional Budget Allocation Matrix Interface
Figure 63 shows the Institutional Budget Allocation Matrix management screen, accessed by Recording Staff and Budget Officers. The interface features a two-tier financial matrix tracking research funds across four categories: *General Appropriations Act (GAA)*, *Local Trust Fund*, *Foreign Trust Fund*, and *Implementation Trust Fund*.

The matrix covers the 75 academic program units across Main Campus and the four satellite campuses (*Libertad*, *Caluya*, *Hamtic*, and *Tibiao*). Staff can input and update annual program budget allocations with real-time automatic computation of program subtotals and institutional grand totals, and export print-ready Institutional Budget Allocation Sheets.

### Figure 68. Budget Officer Dashboard
Figure 68 illustrates the Budget Officer Dashboard, representing the financial gatekeeping portal of the platform. The dashboard displays all proposals that have cleared technical evaluation and Dean noting, awaiting fiscal validation.

The Budget Officer inspects the proposal's itemized Line-Item Budget across Personnel Services, MOOE, and Capital Outlay, verifying the figures against approved campus fund allocations. Upon verification, the officer executes "Certify Fund Availability," applying a digital financial endorsement that advances the proposal to the Research Director and VPREI for final approval.

### Figure 67. Research Director (Admin) Dashboard
Figure 67 shows the Research Director Dashboard, which centralizes operational administration. The dashboard displays university-wide KPI cards, pending user registration approvals, and proposals requiring administrative action across all five lifecycle phases.

The Director utilizes this interface to assign qualified peer reviewers based on subject-matter expertise, oversee revision cycles, endorse cleared dossiers to the VPREI, and publish institutional Call for Papers announcements. The portal provides comprehensive executive control over university research operations.

### Figure 74. VPREI Executive Dashboard
Figure 74 depicts the Vice President for Research, Extension, and Innovation (VPREI) Dashboard. Designed for high-level governance, the interface displays institutional research productivity metrics, budget utilization trends, and the Executive Dossier Approval Queue.

The VPREI inspects the complete audit trail of director-cleared proposals, including reviewer rubric scores, Dean endorsements, and budget certifications. Clicking "Grant Final Executive Approval" updates the proposal status and triggers the DomPDF engine to automatically compile and issue the official Notice of Acceptance (NOA) and Notice to Proceed (NTP), authorizing project commencement.

---

## 5. Data Analysis Plan & Formulas (Page 23–24 of Guide)

The evaluation data collected from the standardized Likert-scale instruments were analyzed using descriptive statistics, specifically the **Weighted Mean ($WM$)** and **Sample Standard Deviation ($s$)**.

### Weighted Mean Formula:
$$WM = \frac{\sum fx}{N}$$
Where:
* $f$ = frequency of responses for each rating category
* $x$ = numerical weight assigned to the response (1 to 5)
* $N$ = total number of respondents

### Sample Standard Deviation Formula:
$$s = \sqrt{\frac{\sum (x_i - \bar{x})^2}{n - 1}}$$
Where:
* $x_i$ = individual rating score from respondent $i$
* $\bar{x}$ = computed sample mean
* $n$ = total sample size ($n = 30$ for end-users, $n = 5$ for experts)

### Statistical Interpretation:
The standard deviation measures the dispersion of ratings around the mean. A lower standard deviation ($s < 0.65$) indicates that respondents' ratings are tightly clustered around the mean, demonstrating high consensus among stakeholders regarding software quality. A higher standard deviation reflects greater variability in responses.

---

## 6. Implementation Plan & Risk Mitigation Table (Pages 24–27 of Guide)

### Table 12. Implementation Risk and Mitigation Matrix
| Identified Risk | Probability | Impact | Mitigation Strategy |
|:---|:---:|:---:|:---|
| **Intermittent Internet Connectivity** | Medium | High | Implement client-side local caching via Alpine.js/LocalStorage and provide offline draft saving to prevent data loss during network drops. |
| **Server / Cloud Service Downtime** | Low | High | Utilize Supabase managed cloud infrastructure with automated database backups, high-availability multi-region replication, and failover support. |
| **User Resistance to Digital Adoption** | Medium | Medium | Conduct a structured 5-session end-user training program across all campuses, provide printable step-by-step user manuals, and establish dedicated helpdesk support. |
| **Data Loss or Accidental Deletion** | Low | High | Enforce automated daily database snapshots on Supabase Cloud, restrict hard deletion privileges exclusively to Super Admins, and maintain immutable Spatie audit logs. |
| **Unauthorized Access / Privilege Escalation** | Low | High | Implement strict server-side `RoleMiddleware` verification, Bcrypt password hashing, session expiration guards, and time-signed temporary URLs for all document downloads. |
| **Technology Dependency & Version Drift** | Low | Medium | Utilize Composer and NPM lockfiles (`composer.lock`, `package-lock.json`) to pin exact package versions and maintain comprehensive developer documentation. |

---

## 7. End-User Training Program Structure (Page 27 of Guide)

To ensure seamless institutional rollout across the University of Antique Main Campus and the four satellite campuses (*Libertad*, *Caluya*, *Hamtic*, *Tibiao*), the implementation plan incorporates a five-session training program:

* **Session 1 – System Introduction & Institutional Governance:**
  * Orientation to the URLC platform purpose, benefits, and digital transformation goals.
  * Overview of the 9 institutional roles and their respective administrative mandates.
* **Session 2 – User Onboarding & Basic Navigation:**
  * Account registration, profile setup, and credential management.
  * Navigating role-specific dashboards, notifications, and announcement feeds.
* **Session 3 – Proposal Lifecycle & Document Management:**
  * Pre-award proposal encoding, dynamic Line-Item Budgeting, and PDF manuscript uploading.
  * Departmental screening, technical reviewer rubric scoring, revision resubmission loops, and automated status tracking.
* **Session 4 – Financial Auditing & Executive Award Issuance:**
  * Dean noting workflows, Budget Officer fund availability certification, and multi-campus budget matrix management.
  * VPREI final executive approval and downloading automated DomPDF legal instruments (Notice of Acceptance and Notice to Proceed).
* **Session 5 – Implementation Monitoring, Archiving & Troubleshooting:**
  * Milestone submissions, Activity Design reviews, and Purchase Request tracking.
  * Terminal report verification, Certificate of Completion issuance, and public repository search.
  * Addressing common user validation errors, browser compatibility, and technical support procedures.
