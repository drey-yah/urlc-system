# CHAPTER III
# DESIGN AND METHODOLOGY

This chapter presents the design and methodology utilized in the development, testing, evaluation, and implementation planning of the **University Research Lifecycle – Digital Platform (URLC System)**. It details the system development methodology, the requirements engineering and analysis process, the technical architectural background, the structured and object-oriented modeling diagrams, the database and user-interface design, the development and verification environment, the formal software quality evaluation instrument, and the operational cloud implementation plan.

---

## 3.1 Development Methodology

The development of the URLC System utilized the **Agile Software Development Life Cycle (SDLC)** methodology. In academic software engineering and enterprise research administration, requirements frequently evolve based on administrative policies, institutional hierarchies, and stakeholder feedback. Agile was selected over rigid predictive models (such as traditional Waterfall) because it prioritizes iterative delivery, continuous stakeholder collaboration, and flexibility in incorporating regulatory enhancements without disrupting core system modules (Sommerville, 2016).

```
        ┌─────────────────────────────────────────────────────────┐
        │                 Agile SDLC Sprints                      │
        │                                                         │
        │   ┌──────────────┐     ┌──────────────┐                 │
        │   │ 1. Sprint    │ ──> │  2. Design   │ ──┐             │
        │   │   Planning   │     │  & Modeling  │   │             │
        │   └──────────────┘     └──────────────┘   │             │
        │          ▲                                │             │
        │          │                                ▼             │
        │   ┌──────────────┐     ┌──────────────┐ ┌─────────────┐ │
        │   │ 5. Review &  │ <── │ 4. Testing & │ │ 3. Modular  │ │
        │   │   Retrospect │     │  Validation  │ │ Development │ │
        │   └──────────────┘     └──────────────┘ └─────────────┘ │
        └─────────────────────────────────────────────────────────┘
```
**Figure 5. Agile Software Development Life Cycle Model**

### Application of the Agile Model to the Project
Rather than treating development as a single sequential pass, the project was executed across five iterative two-week sprints. The proponents actively engaged institutional stakeholders from the University of Antique Research Unit, college departments, the budget office, and university executive leadership.

1. **Sprint 1 (Requirements Elicitation & Architectural Prototyping):** The proponents conducted structured interviews with faculty researchers, college deans, and research administrators. The activities produced the functional requirements baseline, non-functional quality criteria, and initial database entity-relationship models.
2. **Sprint 2 (Authentication, RBAC & Phase 1 Proposal Intake):** Developed user registration approval workflows, Bcrypt authentication, multi-role session switching, proposal encoding forms, and dynamic Line-Item Budgeting (LIB). Evaluated by faculty researchers and college coordinators to refine form validation.
3. **Sprint 3 (Phase 2 Technical Evaluation & Rubric Scoring):** Developed reviewer assignment logic, standardized quantitative rubric scoring, qualitative comment threads, and the manuscript revision resubmission pipeline. Evaluated with faculty reviewers.
4. **Sprint 4 (Phase 3 Dean Noting, Budget Certification & Phase 4 Executive Approval):** Integrated the two-tier **Institutional Budget Allocation Matrix** (GAA, Local Trust, Foreign Trust, Implementation Trust across 75 academic program units and 4 satellite campuses), Budget Officer fund certification, VPREI executive approval, and automated **DomPDF** generation of the Notice of Acceptance (NOA) and Notice to Proceed (NTP).
5. **Sprint 5 (Phase 5 Implementation Monitoring, Archiving & Cloud Deployment):** Constructed milestone submission queues, Activity Designs, Purchase Request tracking, Terminal Report archiving into the searchable public repository, and integrated Spatie ActivityLog audit trails. Staged the final application on Supabase Cloud.

---

## 3.2 Requirements Analysis and Documentation

### 3.2.1 Functional Requirements Specification
Functional requirements define the core operational behaviors, business logic, and automated services executed by the URLC platform. In accordance with institutional research governance, the functional scope is itemized in Table 1 with assigned priorities and acceptance indicators.

#### Table 1. Functional Requirements Specification
| ID | Functional Requirement | Priority | Verifiable Acceptance Indicator |
|:---:|:---|:---:|:---|
| **FR-01** | The system shall authenticate authorized users and enforce Role-Based Access Control (RBAC) across nine specialized roles and a pre-seeded Super Admin. | High | Valid credentials authenticate the user; session middleware restricts route access strictly to assigned role dashboards; unauthorized access triggers HTTP 403 Forbidden. |
| **FR-02** | The system shall require Super Admin approval for newly registered administrative, staff, and evaluator accounts prior to account activation. | High | Unapproved accounts attempting login receive an "Account Pending Approval" prompt; status transitions to 'Active' upon administrator verification. |
| **FR-03** | The system shall enable faculty researchers to encode proposal metadata, itemize Line-Item Budgets (LIB: PS, MOOE, CO), and upload PDF manuscripts. | High | Proposal records are saved to PostgreSQL; manuscript files are stored securely in Supabase S3 cloud storage with time-signed access links. |
| **FR-04** | The system shall route submitted proposals to the designated College Coordinator for departmental screening and endorsement. | High | Proposals populate the coordinator's filtered college queue; coordinator can formally endorse the submission or return it with specific revision remarks. |
| **FR-05** | The system shall allow Support Staff (Receiving) to perform documentary completeness checks on endorsed proposals. | High | Staff can inspect attached checklist documents (ethics clearance, endorsements) and execute "Receive & Forward" to advance the dossier to the Research Director. |
| **FR-06** | The system shall enable the Research Director to assign qualified Technical Reviewers based on subject expertise. | High | Assigned proposals populate the designated reviewer's queue; an automated email alert is dispatched via Laravel Queue Jobs. |
| **FR-07** | The system shall provide reviewers with an interactive Evaluation Rubric to record quantitative scores and structured qualitative comments. | High | Rubric scores, comments, suggestions, and binding review decisions (Approve, Revision Required, Reject) are immutably stored and displayed to the researcher. |
| **FR-08** | The system shall manage version-controlled resubmissions when reviewers request manuscript revisions. | High | Researchers can upload revised drafts (`ProposalRevision`); previous versions remain permanently accessible in the historical audit trail. |
| **FR-09** | The system shall allow the College Dean to note final revised copies and the Budget Officer to certify institutional fund availability. | High | Dean noting updates proposal status; Budget Officer audits line items against fund ceilings and sets status to `funds_certified`. |
| **FR-10** | The system shall enable the Research Director to endorse cleared dossiers to the VPREI for final executive approval. | High | Executive queue displays full dossier history; VPREI can execute binding executive approval or disapproval with formal rationale. |
| **FR-11** | The system shall automatically compile and generate standardized, publication-grade PDF legal instruments (NOA, NTP, Certificate of Completion) via DomPDF. | High | Upon VPREI approval, styled PDF documents with institutional headers and dynamic data are generated server-side and made downloadable. |
| **FR-12** | The system shall provide an Institutional Budget Allocation Matrix tracking funds (GAA, Local Trust, Foreign Trust, Implementation Trust) across 75 academic programs and 4 satellite campuses. | Medium | Budget allocations compute live program subtotals and institutional grand totals with print-ready summary export. |
| **FR-13** | The system shall provide an interactive Announcement Module for publishing Calls for Papers and review schedules with likes and comments. | Medium | Announcements display banner images; users can post comments and toggle likes with real-time counter updates. |
| **FR-14** | The system shall record critical user actions, state changes, and timestamps into an immutable audit trail using Spatie ActivityLog. | High | Audit table records user ID, IP address, event type, and before-and-after change sets viewable by administrators. |

---

### 3.2.2 Non-Functional Requirements Specification (ISO/IEC 25010:2023)
Non-functional requirements address quality characteristics, performance benchmarks, and environmental constraints defined by the ISO/IEC 25010:2023 product quality standard.

#### Table 2. Non-Functional Requirements Specification
| Quality Characteristic | Requirement Specification | Verifiable Metric / Standard |
|:---|:---|:---|
| **Functional Suitability** | The platform must comprehensively cover all five phases of the university research lifecycle as defined by University of Antique research policy. | 100% of mandated institutional approval gates and document checkpoints implemented. |
| **Performance Efficiency** | Page render times and database query responses must execute within 2 seconds under standard campus network bandwidth. | Sub-2.0s server response time achieved via server-side Blade caching and indexed database queries. |
| **Compatibility** | The system must operate consistently across all major modern web browsers and seamlessly interface with Supabase S3 cloud services. | 100% functional consistency across Google Chrome, Microsoft Edge, Mozilla Firefox, and Apple Safari. |
| **Usability** | User interfaces must feature intuitive layouts, responsive cards, clear visual progress indicators, and minimal cognitive load for faculty and staff. | Evaluated with a target usability mean rating $\ge 4.20$ on a 5-point Likert scale. |
| **Reliability & Fault Tolerance** | Database transactions must enforce ACID compliance to prevent orphaned records or partial writes during multi-tier routing. | Zero orphaned records; automatic database rollback on query failures. |
| **Security** | Multi-layered defense comprising HTTPS encryption, CSRF tokens, Bcrypt password hashing, PDO parameterized queries, and role-scoped middleware. | Zero privilege escalation vulnerabilities; all administrative routes guarded by `RoleMiddleware`. |
| **Maintainability** | Decoupled Model-View-Controller architecture with standardized Eloquent migrations and modular service providers. | Codebase adheres strictly to PSR-12 PHP coding standards. |
| **Portability** | Responsive frontend layouts adapt dynamically to desktop, laptop, and tablet viewports via Tailwind CSS and Bootstrap 5.3. | Fluid viewports ranging from 768px (tablets) to 1920px (desktop monitors). |

---

### 3.2.3 Current Technical Situation
Prior to the development of the URLC System, the University of Antique Research Unit managed research proposals, evaluations, budget verifications, and monitoring through manual and semi-digital methods. 

```
┌──────────────┐     ┌──────────────┐     ┌──────────────┐     ┌──────────────┐
│  Researcher  │ ──> │ Support Staff│ ──> │  Reviewers   │ ──> │  Dean/Budget │
│ Submits via  │     │ Logs in MS   │     │ Evaluate via │     │ Manual Excel │
│ Paper/Email  │     │ Excel Sheets │     │ Paper Forms  │     │ Verification │
└──────────────┘     └──────────────┘     └──────────────┘     └──────────────┘
                                                                       │
                                                                       ▼
┌──────────────┐     ┌──────────────┐     ┌──────────────┐     ┌──────────────┐
│ Institutional│ <── │ Secretarial  │ <── │ VPREI Manual │ <── │   Director   │
│ Delay/Loss   │     │ Paper NTP    │     │ Signature    │     │ Endorsement  │
└──────────────┘     └──────────────┘     └──────────────┘     └──────────────┘
```
**Figure 6. Current Technical Process of the University Research Unit**

#### Description of the Current Technical Situation:
As illustrated in Figure 6, the legacy operational workflow relied heavily on physical paper submissions and disconnected digital tools. Faculty researchers prepared proposals in word processors, printed multiple hard copies, and submitted them in person to the Research Unit or emailed raw PDF attachments. Support staff manually logged incoming submissions into standalone Microsoft Excel workbooks and uploaded documents into disparate Google Drive folders. Reviewer assignments were communicated through verbal requests or separate email threads, and evaluators completed handwritten critique sheets.

This fragmented technical environment presented severe operational bottlenecks. Document routing lacked transparency, leaving researchers unable to track proposal progress. Manual spreadsheet updating introduced data discrepancies, while misplaced paper files frequently caused extended evaluation delays. Furthermore, budget verification required manual comparison against university ledgers, and official instruments (such as the Notice to Proceed) required manual secretarial drafting and physical signature routing. These limitations demonstrated the critical need for a centralized, role-governed digital platform.

---

### 3.2.4 Technical Background and System Architecture

The URLC System was architected as a secure, five-tier enterprise web application designed to decouple presentation, business logic, security controls, and cloud data persistence.

```
┌────────────────────────────────────────────────────────────────────────┐
│ Tier 1: User Presentation Tier                                         │
│ Web Browsers on Desktop, Laptop, and Mobile Viewports                  │
│ [9 Specialized Institutional Roles + Super Admin]                      │
└───────────────────────────────────┬────────────────────────────────────┘
                                    │ HTTPS (Encrypted HTTP Traffic)
┌───────────────────────────────────▼────────────────────────────────────┐
│ Tier 2: Laravel Security Gate & Middleware Tier                        │
│ Bcrypt Hashing │ RoleMiddleware │ ApprovalMiddleware │ CSRF Defense    │
└───────────────────────────────────┬────────────────────────────────────┘
                                    │ Authorized Session State
┌───────────────────────────────────▼────────────────────────────────────┐
│ Tier 3: Presentation Rendering Layer                                   │
│ Laravel Blade Engine │ Tailwind CSS │ Bootstrap 5.3 │ Alpine.js / Axios│
└───────────────────────────────────┬────────────────────────────────────┘
                                    │ Modular Controller Routing
┌───────────────────────────────────▼────────────────────────────────────┐
│ Tier 4: Laravel 8 MVC Application Core (PHP 8)                         │
│ Proposal Intake │ Review Engine │ Budget Matrix │ DomPDF │ Spatie Audit│
└───────────────────────────────────┬────────────────────────────────────┘
                                    │ TCP/IP Port 6543 / HTTPS S3 API
┌───────────────────────────────────▼────────────────────────────────────┐
│ Tier 5: Supabase Cloud Infrastructure Tier                             │
│ Supabase PostgreSQL DB │ Supabase S3 Object Storage │ Gmail SMTP Queue │
└────────────────────────────────────────────────────────────────────────┘
```
**Figure 7. URLC System Architecture**

#### System Architecture Description:
Figure 7 illustrates the five integrated architectural tiers of the platform:
1. **Tier 1 (User Tier):** Accommodates the nine specialized user roles interacting with the system through modern web browsers over HTTPS.
2. **Tier 2 (Security Gate Tier):** Powered by Laravel middleware, this tier intercepts incoming requests to enforce Bcrypt password hashing, session role verification, registration approval checks, and CSRF token validation.
3. **Tier 3 (Presentation Layer):** Dynamically compiles server-side Blade templates styled with Tailwind CSS and Bootstrap 5.3, enriched with Alpine.js for lightweight client-side reactivity without page reloads.
4. **Tier 4 (Application Core):** Encapsulates the core business logic across dedicated domain modules following the Model-View-Controller (MVC) pattern.
5. **Tier 5 (Cloud Infrastructure):** Decouples relational data persistence (Supabase PostgreSQL via direct TCP connection pooling on port 6543) from heavy binary file storage (Supabase S3-compatible cloud storage via the Flysystem S3 driver) and offloads email dispatches to asynchronous Laravel Queue background workers.

#### Project Technicality:
The technical complexity of the URLC platform centered on three major engineering challenges:
1. **Multi-Role Session Governance:** Accommodating institutional users who hold concurrent appointments (e.g., a faculty member who is simultaneously a Researcher, College Coordinator, and Technical Reviewer) required dynamic session role switching without privilege escalation.
2. **Decoupled Cloud Object Security:** Intellectual property in research manuscripts required strict protection against unauthorized public URL access. The proponents implemented the Flysystem S3 driver to generate temporary, time-signed download URLs that expire after 15 minutes.
3. **Server-Side PDF Compilation:** Generating publication-grade official legal documents (NOA, NTP, and Certificate of Completion) required binding dynamic database attributes into vector-rendered templates using Laravel DomPDF.

#### Details and Relevance of Technologies Used:
* **Backend Framework (Laravel v8.75 with PHP 8):** Selected for its robust routing, dependency injection, Eloquent ORM, built-in validation pipelines, and architectural security against SQL injection and CSRF attacks.
* **Database (PostgreSQL 15+ on Supabase Cloud):** Chosen for strict ACID compliance, relational integrity, robust JSON casting for multi-role arrays, and zero local database maintenance.
* **Cloud Storage (Supabase S3-Compatible Storage):** Selected to eliminate local host disk bottlenecks and secure manuscript binary payloads.
* **Frontend UI (Blade, Tailwind CSS, Bootstrap 5.3, Alpine.js):** Provided responsive, accessible user interfaces with lightweight asynchronous reactivity.
* **Audit Logging (Spatie ActivityLog):** Selected to provide immutable institutional audit trails capturing user IDs, IP addresses, and state changes.

#### How the System Works (User Perspective):
The platform automates the research lifecycle across five sequential stages:
* **Step 1 (Submission & College Intake):** A faculty researcher registers, encodes proposal metadata, itemizes the Line-Item Budget (LIB), and uploads the manuscript. The proposal routes to the College Coordinator for departmental review and Support Staff (Receiving) for completeness verification.
* **Step 2 (Technical & Ethics Evaluation):** The Research Director assigns peer evaluators who review the manuscript and submit standardized rubric scores and qualitative comments. Revisions loop back to the researcher for resubmission.
* **Step 3 (Dean Noting & Budget Certification):** The College Dean notes the final revised copy, and the Budget Officer audits the Line-Item Budget against institutional fund ceilings (GAA, Local Trust, Foreign Trust, Implementation Trust) to certify funding availability.
* **Step 4 (Executive Approval & Award Issuance):** The Research Director endorses the dossier to the VPREI, who grants final executive approval, automatically triggering DomPDF to compile official NOA and NTP documents.
* **Step 5 (Implementation Monitoring & Repository Archiving):** Researchers submit milestone deliverables, Activity Designs, and Purchase Requests. Upon project completion, the Terminal Report is verified, a Certificate of Research Completion is generated, and the study is archived in the searchable public repository.

---

## 3.3 Requirement Modeling

### 3.3.1 Structured Diagrams

#### Figure 8. Functional Decomposition Diagram (FDD)
Figure 8 illustrates the hierarchical functional decomposition of the URLC platform. The system decomposes into seven primary operational modules: User and Access Governance, Proposal Intake and Routing, Review and Evaluation, Budget and Financial Certification, Approvals and Document Generation Engine, Milestone and Repository Management, and Communication and System Administration. Each core domain branches into modular sub-functions, illustrating how complex institutional responsibilities are partitioned into discrete subsystems.

This decomposition establishes clear functional boundaries across the software engineering lifecycle. By isolating responsibilities—such as separating Line-Item Budget validation from peer review scoring—the diagram provides a clear architectural blueprint for developers. It ensures that every operational requirement identified during stakeholder interviews maps directly to a discrete set of backend controllers and user interface components.

#### Figure 9. System Context Diagram (Level 0 DFD)
Figure 9 depicts the System Context Diagram of the URLC platform, establishing the functional boundary of the system and illustrating the external actors that exchange data with it. At the center is the URLC Digital Platform, acting as the primary transaction and workflow engine. Surrounding the platform are the nine institutional stakeholders and the Supabase Cloud Infrastructure persistence layer, linked via directed data flows.

The diagram illustrates the primary inputs and outputs of the platform. Faculty Researchers submit proposal metadata, line-item budgets, and manuscripts, receiving status alerts and generated notices. Evaluators receive assigned manuscripts and submit rubric scores. The College Dean and Budget Officer provide endorsements and financial certifications, while the VPREI executes final executive approval. Supabase Cloud persists relational queries and streams binary document payloads.

#### Figure 9-B. Data Flow Diagram (DFD) Level 1
*(Refer to Manuscript DFD Level 1 Specifications)*

**Figure 9-B. Data Flow Diagram Level 1**

Figure 9-B details the internal data transformations within the URLC System, decomposing the Level 0 context diagram into six core sub-processes: Process 1.0 (User Authentication & RBAC Governance), Process 2.0 (Proposal Intake & Departmental Routing), Process 3.0 (Technical Evaluation & Rubric Feedback), Process 4.0 (Budget Auditing & Fiscal Certification), Process 5.0 (Executive Approval & DomPDF Legal Document Issuance), and Process 6.0 (Post-Award Monitoring & Repository Archival). Each process interacts with distinct database entities (`users`, `research_proposals`, `proposal_budget_items`, `proposal_reviews`, `budget_allocations`, `activity_log`).

The diagram demonstrates the sequential data dependencies governing the research lifecycle. For example, Process 4.0 cannot execute fund certification until Process 3.0 stores verified evaluator rubric scores and Process 2.0 confirms College Dean noting. Similarly, Process 5.0 retrieves validated budget data to dynamically compile the Notice to Proceed PDF document and populate the public repository datastore.

#### Figure 10. System Flowchart (Swimlanes)
Figure 10 presents the System Flowchart structured into color-coded swimlanes representing the nine institutional roles and the underlying technical system core. The diagram details sequential operational logic, decision diamonds, automated status transitions, and document exchanges spanning all five formal phases of the university research lifecycle.

The swimlane structure emphasizes procedural rigor and automated handoffs. If a proposal is deemed deficient at any stage—such as during coordinator screening or technical peer review—decision diamonds route the submission back into the Researcher lane with revision remarks, preventing further progression until compliance is re-verified. Once the VPREI lane executes final executive approval, the System Core automatically triggers the DomPDF engine to render official Notice of Acceptance and Notice to Proceed documents.

---

### 3.3.2 Object-Oriented Diagrams

#### Use Case Diagrams (Figures 11–20)
* **Figure 11. Faculty Researcher Use Case:** Models researcher interactions organized into Account Management, Proposal Management (`<<include>>` metadata, LIB, PDF upload; `<<extend>>` save draft, add co-authors, revise/resubmit), Implementation Monitoring, Document Downloads, and Repository Browsing.
* **Figure 12. College Coordinator Use Case:** Models departmental quality-control interactions including viewing college-filtered proposals, inspecting manuscripts, and executing endorsements or returning submissions with revision notes.
* **Figure 13. Support Staff (Receiving) Use Case:** Illustrates intake verification, document completeness checking (endorsements, ethics clearances), and forwarding verified submissions to the Research Director.
* **Figure 15. Technical Reviewer Use Case:** Models peer review interactions including downloading manuscripts via secure links, filling standardized evaluation rubrics, writing qualitative comments, and submitting review decisions (*Approve*, *Revision Required*, *Reject*).
* **Figure 16. College Dean Use Case:** Illustrates collegiate oversight, reviewing college research metrics, and noting initial endorsements (Phase 1) and post-evaluation final copies (Phase 3).
* **Figure 17. Budget Officer Use Case:** Defines financial gatekeeping workflows including Line-Item Budget auditing against fund caps (GAA, Trust Funds), executing "Certify Fund Availability", and generating institutional budget summaries.
* **Figure 18. Research Director (Admin) Use Case:** Depicts central operational administration including assigning evaluators, managing lifecycle phase transitions, endorsing dossiers to the VPREI, and publishing Call for Papers bulletins.
* **Figure 19. VPREI Executive Use Case:** Models executive governance including reviewing director-cleared dossiers, executing final executive approval, authorizing Notice to Proceed issuance, and inspecting university-wide research KPIs.
* **Figure 20. Support Staff (Recording) Use Case:** Details read-only tracking via the Routing Tracker, monitoring active milestone deliverables, managing the Institutional Budget Matrix, and cataloging completed studies into the repository.
* **Figure 20-B. Super Admin Use Case:** Illustrates top-level platform governance, pre-seeded account login, approving pending staff registrations, user role reassignments, system settings configuration, and Spatie audit log inspection.

#### Activity Diagrams (Figures 21–29)
The activity diagrams visually depict the step-by-step procedural workflows, conditional logic, and state transitions for each stakeholder:
* **Figure 21. Researcher Activity Diagram (`Pages A.1–A.3`):** Details account registration, proposal metadata input, Line-Item Budget itemization, manuscript PDF upload, revision resubmission loops, milestone progress submissions, and terminal report completion.
* **Figure 22. Coordinator Activity Diagram (`Pages B.1–B.2`):** Details college proposal filtering, manuscript inspection, endorsement execution, and Activity Design review.
* **Figure 23. Support Staff (Receiving) Activity Diagram (`Pages C.1–C.2`):** Models account approval, intake queue monitoring, attachment checklist verification, and forwarding to admin.
* **Figure 24. Reviewer Activity Diagram (`Pages D.1–D.2`):** Illustrates manuscript download, rubric criteria evaluation, qualitative feedback entry, and milestone deliverable evaluation.
* **Figure 24-B. College Dean Activity Diagram (`Pages E.1–E.2`):** Details college metrics oversight, Phase 1 initial endorsement noting, and Phase 3 post-evaluation final copy noting to the Budget Office.
* **Figure 25. Budget Officer Activity Diagram (`Pages F.1–F.2`):** Illustrates queue inspection, Line-Item Budget auditing across PS, MOOE, and CO against institutional fund ceilings, and executing fund availability certification.
* **Figure 26-B. Support Staff (Recording) Activity Diagram (`Pages G.1–G.3`):** Illustrates Routing Tracker monitoring, ongoing research milestone tracking, repository cataloging, and administering the two-tier Institutional Budget Allocation Matrix.
* **Figure 27. Research Director Activity Diagram (`Pages H.1–H.4`):** Models reviewer matching, Phase 2 intake, Phase 4 VPREI endorsement, Phase 5 milestone oversight, user management, and announcement publishing.
* **Figure 28. VPREI Activity Diagram (`Pages I.1–I.2`):** Illustrates executive intelligence review, dossier inspection, final executive approval execution, and automated NOA/NTP issuance.
* **Figure 29. Super Admin Activity Diagram (`Pages J.1–J.3`):** Models pre-seeded authentication, staff registration verification, user role assignment, system setting configuration, and Spatie audit log auditing.

#### Sequence Diagrams (Figures 29-B to 29-D)
To detail temporal object interactions across the Laravel MVC architecture, three sequence diagrams were modeled:
1. **Authentication & Multi-Role Session Routing Sequence:** Traces user credential submission through `AuthenticatedSessionController`, `RoleMiddleware` session verification, and redirection to role dashboards.
2. **Proposal Submission & Cloud Persistence Sequence:** Illustrates the interactions between the Researcher, `ResearchProposalController`, PostgreSQL relational database, and the Supabase S3 Flysystem driver.
3. **Executive Approval & DomPDF Document Generation Sequence:** Traces the VPREI approval action, status database update, invocation of `DomPDF` compiler, and streaming of the generated Notice to Proceed PDF to the user portal.

---

## 3.4 Database and User-Interface Design

### 3.4.1 Entity Relationship Diagram (ERD)
Figure 26 presents the logical database schema of the URLC platform, normalized to Third Normal Form (3NF). The schema contains sixteen primary tables grouped into four domains:
* **User Identity & Governance:** `users`, `user_roles`, `proposal_collaborators`, `proposal_assignments`
* **Proposal Lifecycle & Revisions:** `research_proposals`, `proposal_documents`, `proposal_revisions`, `proposal_reviews`
* **Institutional Budgeting:** `proposal_budget_items`, `budget_allocations`
* **Implementation, Closeout & Governance:** `research_milestones`, `activity_designs`, `purchase_requests`, `terminal_reports`, `announcements`, `announcement_interactions`, `activity_log`, `system_settings`

Foreign key constraints and cascading rules guarantee data consistency across all lifecycle transitions.

---

### 3.4.2 User-Interface Design Descriptions (Figures 31–86)

In accordance with the revision guide, each user interface screenshot is documented with a comprehensive academic description:

* **Figure 31. User Register and Login Form:** Serves as the unified authentication gate for all institutional users, providing registration forms with college/campus selection and credential login with CSRF defense and Bcrypt verification.
* **Figure 32. Faculty Researcher's Dashboard:** Features four real-time KPI cards (Total Submissions, Under Review, Revisions Needed, Approved) and an active proposals table displaying dynamic status badges and quick-action navigation.
* **Figure 33. Researcher Researches Module:** Provides multi-criteria filtering by lifecycle phase, status, and fiscal year, allowing faculty members to monitor proposal progression and inspect past revisions.
* **Figure 34. Researcher Submission Module:** Facilitates new proposal intake, validating metadata (title, abstract, agenda alignment), dynamic Line-Item Budget encoding (PS, MOOE, CO), and drag-and-drop PDF manuscript uploading (max 20MB).
* **Figure 35. Researcher Proposal Details Module:** Displays the interactive 5-phase visual progress timeline, proposal overview, attached documents, and evaluator critique threads with version-controlled resubmission capabilities.
* **Figure 36. Researcher Announcement Module:** Displays institutional bulletins, Call for Papers announcements, and review schedules, supporting interactive likes and threaded community comments.
* **Figure 37. Centralized Research Document Repository:** Allows users to search, filter, and access published research abstracts and terminal reports stored permanently on Supabase S3.
* **Figure 38 & 39. Researcher Profile & Edit Profile:** Enables faculty members to view account details, update department affiliations, and securely change account passwords.
* **Figure 40. College Coordinator's Dashboard:** Displays departmental research metrics and an Endorsement Center queue isolating submissions originating exclusively from the coordinator's college.
* **Figure 41 & 42. Coordinator Announcement & Repository Modules:** Provides coordinators with access to institutional bulletins and the university-wide research repository.
* **Figure 43. Coordinator Proposal Details Module:** Allows coordinators to examine attached manuscripts, verify college agenda alignment, and execute endorsements or return submissions with revision remarks.
* **Figure 44 & 45. Coordinator Profile & Edit Profile:** Manages coordinator profile details and authentication credentials.
* **Figure 46. College Dean's Dashboard:** Aggregates collegiate research KPIs and displays operational queues for Phase 1 Initial Endorsement Noting and Phase 3 Post-Evaluation Final Copy Noting.
* **Figure 47. Dean's View Proposal Details:** Enables deans to inspect reviewer rubric clearances and verified manuscript revisions before executing official collegiate concurrence to the Budget Office.
* **Figure 48 & 49. Dean's Profile & Repository Access:** Manages executive profile configurations and repository browsing.
* **Figure 50. Support Staff (Receiving) Dashboard:** Displays coordinator-endorsed proposals awaiting intake verification and document completeness checking.
* **Figure 51. Support Staff (Receiving) Proposal Details:** Implements an administrative checklist for verifying ethics clearances and endorsement forms before executing "Receive & Forward" to the Research Director.
* **Figure 52–55. Support Staff (Receiving) Bulletins, Repository, and Profile:** Supports intake staff communication, archive lookups, and account management.
* **Figure 56. Technical Reviewer Dashboard & Evaluation Queue:** Lists proposals assigned to the evaluator, enabling secure manuscript downloads and access to the scoring rubric.
* **Figure 57–60. Reviewer Bulletins, Repository, and Profile Views:** Supports reviewer communication, document reference lookups, and account profile maintenance.
* **Figure 61. Reviewer Evaluation and Rubric Interface:** Implements the standardized scoring rubric, qualitative comment boxes, and binding evaluation decision triggers (*Approve*, *Revision Required*, *Reject*).
* **Figure 62. Support Staff (Recording) Dashboard & Routing Tracker:** Features the university-wide Routing Tracker, providing real-time visibility into the exact phase, status, and assigned personnel of every active proposal.
* **Figure 63. Institutional Budget Allocation Matrix Interface:** Manages the two-tier fund allocation matrix across four fund categories (*GAA*, *Local Trust*, *Foreign Trust*, *Implementation Trust*) for 75 academic program units and four satellite campuses, with print-ready summary export.
* **Figure 64. Internal Direct Messaging Module:** Provides threaded, Gmail-style internal communications between researchers, reviewers, coordinators, and administrators.
* **Figure 65–66. Recording Staff Profile Views:** Supports profile maintenance and password updates for recording personnel.
* **Figure 68. Budget Officer Dashboard & Funds Verification:** Displays proposals awaiting financial certification, enabling the Budget Officer to audit Line-Item Budgets against fund caps and execute "Certify Fund Availability".
* **Figure 69–73. Budget Officer Modules & Profile:** Manages budget announcement tracking, repository records, messaging, and account profile settings.
* **Figure 67. Research Director (Admin) Dashboard:** Centralizes administrative governance, displaying global KPI metrics, pending staff approval queues, reviewer assignment triggers, and executive endorsements.
* **Figure 68-B. Research Activity & Audit Logs Module:** Displays immutable Spatie audit trails capturing user IDs, IP addresses, model events, and before-and-after change sets.
* **Figure 69-B. Externally Funded Research Module:** Tracks external grant agency endorsements, agreements, and multi-year funding disbursements.
* **Figure 70–73. Admin Announcement Management & Profiles:** Enables administrators to compose, edit, and delete institutional announcements and manage administrative credentials.
* **Figure 74. VPREI Executive Dashboard:** Presents university-wide research KPIs, budget utilization charts, and the Executive Dossier Queue for final award authorization.
* **Figure 75–79. VPREI Executive Modules & Profile:** Manages executive views for externally funded studies, repository archives, and profile settings.
* **Figure 80. Super Admin Governance Dashboard:** Displays macro-level system metrics, user directories, global settings configuration, and staff account approval queues.
* **Figure 81. Admin View Proposal Details, Assign Reviewer & Decision Module:** Allows administrators to select qualified peer reviewers, oversee revision compliance, and execute director endorsements to the VPREI.
* **Figure 82–86. Super Admin Settings, Repository, and Profile Configuration:** Manages system-wide key-value configuration parameters, repository indexing, and administrative profiles.

---

## 3.5 Development and Testing Environment

### 3.5.1 Software Specification
Table 3 documents the software environment utilized during development and testing.

#### Table 3. Software Environment Specifications
| Software Category | Specification / Tool | Purpose in the Project |
|:---|:---|:---|
| **Operating System** | Windows 11 / Linux (Ubuntu 22.04 LTS) | Development host and production server environments |
| **IDE** | Visual Studio Code (v1.90+) | Primary coding, debugging, and Git integration |
| **Backend Framework** | Laravel Framework (v8.75) | Core MVC application logic, middleware, and Eloquent ORM |
| **Programming Language** | PHP (v8.0.28) | Server-side execution runtime |
| **Database Engine** | PostgreSQL (v15+) on Supabase Cloud | Relational database persistence, indexing, and pooling |
| **Object Storage Driver** | Supabase S3 / Flysystem AWS S3 v3 | Secure cloud storage for manuscripts and legal notices |
| **UI Styling & Reactivity** | Tailwind CSS (v3.1) & Bootstrap (v5.3) | Responsive layouts and accessible styling |
| **Client Scripting** | Alpine.js (v3.4) & Axios (v0.21) | Lightweight frontend reactivity and asynchronous requests |
| **PDF Rendering Engine** | Laravel DomPDF (v2.2) | Server-side compilation of official NOA, NTP, and Certificates |
| **Activity Logging** | Spatie ActivityLog (v4.0) | Automated immutable audit trail tracking |
| **Version Control** | Git & GitHub | Distributed version control and collaborative code review |
| **Package Managers** | Composer (v2.5+) & NPM (v9+) | PHP and JavaScript dependency management |
| **Web Browsers** | Google Chrome, Microsoft Edge, Firefox, Safari | Cross-browser compatibility and responsive viewport testing |

---

### 3.5.2 Hardware Specification
Table 4 defines the minimum hardware specifications required for development and end-user access.

#### Table 4. Hardware Environment Specifications
| Hardware Component | Minimum Development Specification | Minimum End-User Client Specification |
|:---|:---|:---|
| **Processor** | Intel Core i5 (8th Gen) or AMD Ryzen 5 equivalent | Intel Core i3 / Celeron or smartphone SoC |
| **Memory (RAM)** | 8.00 GB DDR4 | 4.00 GB DDR3 (Desktop) / 2.00 GB (Mobile) |
| **Storage** | 256 GB Solid State Drive (SSD) | 16 GB available internal storage |
| **Network** | Broadband Internet / LAN connection ($\ge 10$ Mbps) | Stable Internet connection ($\ge 2$ Mbps) |
| **Display** | $1920 \times 1080$ Full HD resolution | $1366 \times 768$ (Laptop) or responsive mobile viewport |

---

### 3.5.3 Test Planning and Test Cases
The testing phase verified that all functional modules operate correctly and securely. A suite of twenty-five (25) test cases was formulated across five modules: Authentication & User Governance (`TESTCASE_01`), Proposal Submission (`TESTCASE_02`), Support Staff Workflow (`TESTCASE_03`), Admin Governance (`TESTCASE_04`), and Evaluation/Review (`TESTCASE_05`). Testing procedures verified valid inputs, invalid edge cases, role boundary enforcement, and cloud persistence.

---

### 3.5.4 Project Evaluation Instrument and Respondents
To evaluate system performance and software quality, standardized questionnaire instruments were formulated based on two recognized quality frameworks:
1. **ISO/IEC 25010 Software Quality Model:** Administered to thirty (30) end-users to evaluate Functional Suitability, Performance Efficiency, Compatibility, Usability, Reliability, Security, Maintainability, and Portability.
2. **McCall’s Software Quality Model:** Administered to five (5) IT professionals to evaluate Product Operation, Product Revision, and Product Transition technical factors.

#### Respondents of the Study:
The evaluation participants were selected through **purposive sampling**, ensuring that respondents had direct experience with the university research lifecycle:
* **End-User Group ($n = 30$):** Composed of 12 Faculty Researchers, 6 College Research Coordinators, 4 College Deans, 4 Technical Reviewers, 2 Budget Officers, and 2 Research Support Staff across the University of Antique.
* **IT Expert Group ($n = 5$):** Composed of 1 Systems Administrator, 1 Web & LMS Administrator, 2 Junior Developers from the University ICT Unit, and 1 Senior IT Instructor from the College of Computing and Information Sciences.

#### Ethical Considerations:
Participation was entirely voluntary. Informed consent was obtained, and participant anonymity was strictly preserved. Evaluators evaluated the deployed system directly on staging workstations.

---

### 3.5.5 Data Analysis Plan

Evaluation ratings were recorded using a 5-point Likert scale (5 – Strongly Agree, 4 – Agree, 3 – Neutral, 2 – Disagree, 1 – Strongly Disagree). Descriptive statistics—specifically the **Weighted Mean ($WM$)** and **Sample Standard Deviation ($s$)**—were used to analyze the results.

#### Weighted Mean Formula:
$$WM = \frac{\sum fx}{N}$$
Where:
* $f$ = frequency of responses for each rating weight
* $x$ = numerical weight assigned to the response (1 to 5)
* $N$ = total number of respondents

#### Sample Standard Deviation Formula:
$$s = \sqrt{\frac{\sum (x_i - \bar{x})^2}{n - 1}}$$
Where:
* $x_i$ = individual rating score from respondent $i$
* $\bar{x}$ = computed sample mean
* $n$ = sample size ($n = 30$ for end-users, $n = 5$ for experts)

#### Statistical Interpretation:
The standard deviation measures the dispersion of ratings around the mean. A lower standard deviation ($s < 0.65$) indicates that respondents' ratings are closely clustered around the mean, demonstrating strong consensus regarding software quality. A higher value indicates greater variation in opinions. Computed means were interpreted using the approved scale:

#### Table 5. Likert Scale Verbal Interpretation
| Numerical Range | McCall's Expert Interpretation | ISO/IEC 25010 End-User Interpretation |
|:---:|:---:|:---:|
| 4.21 – 5.00 | Excellent / Very Good | Strongly Agree |
| 3.41 – 4.20 | Very Good / Moderate | Agree |
| 2.61 – 3.40 | Good / Fair | Neutral |
| 1.81 – 2.60 | Fair / Poor | Disagree |
| 1.00 – 1.80 | Poor | Strongly Disagree |

---

## 3.6 Implementation Plan

### 3.6.1 Deployment Architecture Diagram
Figure 91 illustrates the physical deployment topology of the URLC platform in a production cloud environment.

```
┌────────────────────────────────────────────────────────┐
│ Client Node (Desktop, Laptop, Tablet, Mobile)          │
│ Execution Environment: Modern Web Browser              │
│ Artifact: URLC Responsive Frontend Interface           │
└───────────────────────────┬────────────────────────────┘
                            │ HTTPS (Encrypted Traffic)
┌───────────────────────────▼────────────────────────────┐
│ Web & Application Server Node                          │
│ Execution Environment: Linux OS / PHP 8 / Laravel Core │
│ Artifact: URLC Core Business Logic & Middleware Gates  │
└──────────────┬───────────────────────────┬─────────────┘
               │ TCP/IP Port 6543          │ HTTPS S3 API
┌──────────────▼────────────┐ ┌────────────▼─────────────┐
│ Supabase Cloud: Database  │ │ Supabase Cloud: Storage  │
│ PostgreSQL RDBMS          │ │ S3-Compatible Object Store│
│ Structured Tables & Logs  │ │ PDF Manuscripts & Notices│
└───────────────────────────┘ └──────────────────────────┘
```
**Figure 91. URLC System Deployment Diagram**

#### Deployment Strategy:
The platform utilizes a **Cloud Deployment Strategy**. The web application server is hosted on a high-availability cloud server running PHP 8 and Nginx/Apache. Relational data persistence is decoupled to Supabase Cloud PostgreSQL, while all PDF research manuscripts and generated legal documents reside in Supabase S3-compatible cloud object storage. This cloud deployment eliminates physical campus server maintenance bottlenecks, guarantees high data availability across all satellite campuses, and ensures secure access via encrypted HTTPS connections.

---

### 3.6.2 Implementation Risks and Mitigation Matrix

#### Table 6. Implementation Risk and Mitigation Matrix
| Identified Risk | Probability | Impact | Mitigation Strategy |
|:---|:---:|:---:|:---|
| **Intermittent Internet Connectivity** | Medium | High | Implement client-side local caching via Alpine.js/LocalStorage and provide offline draft saving to prevent data loss during network drops. |
| **Server / Cloud Service Downtime** | Low | High | Utilize Supabase managed cloud infrastructure with automated database backups, high-availability multi-region replication, and failover support. |
| **User Resistance to Digital Adoption** | Medium | Medium | Conduct a structured 5-session end-user training program across all campuses, provide printable step-by-step user manuals, and establish dedicated helpdesk support. |
| **Data Loss or Accidental Deletion** | Low | High | Enforce automated daily database snapshots on Supabase Cloud, restrict hard deletion privileges exclusively to Super Admins, and maintain immutable Spatie audit logs. |
| **Unauthorized Access / Privilege Escalation** | Low | High | Implement strict server-side `RoleMiddleware` verification, Bcrypt password hashing, session expiration guards, and time-signed temporary URLs for all document downloads. |
| **Technology Dependency & Version Drift** | Low | Medium | Utilize Composer and NPM lockfiles (`composer.lock`, `package-lock.json`) to pin exact package versions and maintain comprehensive developer documentation. |

---

### 3.6.3 End-User Training Program

To ensure smooth institutional adoption across the University of Antique Main Campus (Sibalom) and the four satellite campuses (*Libertad*, *Caluya*, *Hamtic*, and *Tibiao*), the implementation plan incorporates a five-session training curriculum:

* **Session 1 – System Introduction & Institutional Governance:**
  * Orientation to the URLC platform purpose, benefits, and digital transformation goals.
  * Overview of the nine institutional roles and their respective administrative mandates.
* **Session 2 – User Onboarding & Basic Operations:**
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
