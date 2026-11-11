<x-app-layout>
    <div class="mb-3 d-flex align-items-center justify-content-between">
        <a href="{{ route('internally_funded_endorsements.index') }}" class="btn btn-sm btn-light rounded-pill px-3 fw-semibold text-muted d-inline-flex align-items-center gap-1.5 shadow-xs">
            <i class="bi bi-arrow-left"></i> <span>Back to Endorsements</span>
        </a>
    </div>

    <!-- Page Header -->
    <div class="card border-0 shadow-sm rounded-4 p-4 mb-4 bg-white">
        <div class="d-flex align-items-center gap-3">
            <div class="rounded-circle d-flex align-items-center justify-content-center text-primary bg-primary bg-opacity-10 flex-shrink-0" style="width: 50px; height: 50px;">
                <i class="bi bi-file-earmark-plus-fill fs-4"></i>
            </div>
            <div>
                <h1 class="h4 fw-bold text-dark mb-1">Prepare Internally-Funded Research Proposal Endorsement Form</h1>
                <p class="text-muted small mb-0">Select endorsed/approved internally-funded proposals and generate the official dossier for VPREI &amp; VPAF recommending approval.</p>
            </div>
        </div>
    </div>

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show rounded-4 border-0 shadow-xs mb-4">
            <ul class="mb-0 small">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <form action="{{ route('internally_funded_endorsements.store') }}" method="POST" id="endorsementForm">
        @csrf

        <!-- Dossier General Metadata -->
        <div class="card border-0 shadow-sm rounded-4 p-4 mb-4 bg-white">
            <h5 class="fw-bold text-dark mb-3">Dossier Details</h5>
            <div class="row g-3">
                <div class="col-12 col-md-6">
                    <label class="form-label fw-bold small text-dark">Document Title <span class="text-danger">*</span></label>
                    <input type="text" name="title" class="form-control rounded-3" value="INTERNALLY-FUNDED RESEARCH PROPOSAL ENDORSEMENT FORM (AY {{ date('Y') . '-' . (date('Y') + 1) }})" required>
                </div>
                <div class="col-12 col-md-3">
                    <label class="form-label fw-bold small text-dark">Fiscal Year</label>
                    <input type="number" name="fiscal_year" class="form-control rounded-3" value="{{ date('Y') }}">
                </div>
                <div class="col-12 col-md-3">
                    <label class="form-label fw-bold small text-dark">Report Date <span class="text-danger">*</span></label>
                    <input type="date" name="report_date" class="form-control rounded-3" value="{{ date('Y-m-d') }}" required>
                </div>
                <div class="col-12">
                    <label class="form-label fw-bold small text-dark">Director's Special Endorsement Remarks (Optional)</label>
                    <textarea name="remarks" rows="2" class="form-control rounded-3 text-sm" placeholder="e.g. Endorsed following rigorous evaluation and panel recommendation during the University In-House Review session..."></textarea>
                </div>
            </div>
        </div>

        <!-- Proposal Quick-Picker Accordion -->
        <div class="card border-0 shadow-sm rounded-4 p-4 mb-4 bg-white">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
                <div>
                    <h5 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                        <i class="bi bi-check2-square text-primary"></i> 1-Click Proposal Selector
                    </h5>
                    <p class="text-muted small mb-0">Check the proposals below to auto-populate the endorsement table.</p>
                </div>
                <span class="badge bg-light text-muted border rounded-pill px-3 py-1.5" style="font-size: 0.78rem;">
                    {{ $proposals->count() }} Available Proposals
                </span>
            </div>

            @if($proposals->isEmpty())
                <p class="text-muted small font-italic mb-0">No eligible proposals found in the database. You can manually enter proposals below.</p>
            @else
                <div class="table-responsive border rounded-3 mb-2" style="max-height: 280px; overflow-y: auto;">
                    <table class="table table-hover align-middle mb-0" style="font-size: 0.85rem;">
                        <thead class="bg-light sticky-top">
                            <tr>
                                <th class="ps-3" style="width: 40px;">Select</th>
                                <th>Project Code &amp; Title</th>
                                <th>Proponents</th>
                                <th>College</th>
                                <th>Budget</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($proposals as $prop)
                                @php
                                    $collabs = $prop->collaborators ? $prop->collaborators->pluck('name')->toArray() : [];
                                    $allProponents = array_merge([$prop->user->name ?? 'Lead Researcher'], $collabs);
                                    $proponentsStr = implode(', ', $allProponents);
                                    $propBudget = (float) ($prop->budgetItems ? $prop->budgetItems->sum('total_cost') : ($prop->proposed_budget ?? 0));
                                @endphp
                                <tr>
                                    <td class="ps-3">
                                        <input type="checkbox" class="form-check-input proposal-picker-chk" 
                                               data-id="{{ $prop->id }}"
                                               data-title="{{ $prop->title }}"
                                               data-proponents="{{ $proponentsStr }}"
                                               data-college="{{ $prop->college ?? ($prop->user->department ?? 'UA') }}"
                                               data-budget="{{ $propBudget }}">
                                    </td>
                                    <td>
                                        <div class="fw-bold text-dark">{{ $prop->title }}</div>
                                        <div class="text-muted small">{{ $prop->proposal_code ?? 'Proposal #' . $prop->id }}</div>
                                    </td>
                                    <td class="text-muted">{{ Str::limit($proponentsStr, 40) }}</td>
                                    <td><span class="badge bg-light text-dark border">{{ $prop->college ?? ($prop->user->department ?? 'UA') }}</span></td>
                                    <td class="fw-bold text-dark">₱{{ number_format($propBudget, 2) }}</td>
                                    <td>
                                        <span class="badge bg-light text-muted border text-capitalize" style="font-size: 0.72rem;">
                                            {{ str_replace('_', ' ', $prop->status) }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>

        <!-- Official Endorsement Form Table Builder -->
        <div class="card border-0 shadow-sm rounded-4 p-4 mb-4 bg-white">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
                <div>
                    <h5 class="fw-bold text-dark mb-0">Endorsement Proposal Line Items</h5>
                    <p class="text-muted small mb-0">These items will appear on the official form and printable PDF.</p>
                </div>
                <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-3 py-1.5 fw-bold" onclick="addCustomRow()">
                    <i class="bi bi-plus-lg me-1"></i> Add Manual Row
                </button>
            </div>

            <div class="table-responsive">
                <table class="table table-bordered align-middle mb-0" id="itemsTable" style="font-size: 0.88rem;">
                    <thead class="bg-light text-dark">
                        <tr>
                            <th style="width: 5%; text-align: center;">#</th>
                            <th style="width: 40%;">Project Title <span class="text-danger">*</span></th>
                            <th style="width: 25%;">Proponent/s <span class="text-danger">*</span></th>
                            <th style="width: 12%;">College</th>
                            <th style="width: 15%;">Budget (Php) <span class="text-danger">*</span></th>
                            <th style="width: 3%; text-align: center;"></th>
                        </tr>
                    </thead>
                    <tbody id="itemsTableBody">
                        <!-- Rows injected via JS or server -->
                    </tbody>
                    <tfoot>
                        <tr class="bg-light fw-bold text-dark">
                            <td colspan="4" class="text-end pe-3">TOTAL PROPOSED BUDGET:</td>
                            <td>
                                <span class="h6 fw-bold text-primary mb-0" id="displayTotal">₱0.00</span>
                            </td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <div id="noItemsAlert" class="alert alert-warning rounded-3 border-0 mt-3 p-2.5 small d-flex align-items-center gap-2">
                <i class="bi bi-exclamation-circle-fill"></i> Please select at least one proposal from above or click "+ Add Manual Row".
            </div>
        </div>

        <!-- Form Actions -->
        <div class="card border-0 shadow-sm rounded-4 p-4 bg-white d-flex flex-wrap flex-row justify-content-between align-items-center gap-2">
            <a href="{{ route('internally_funded_endorsements.index') }}" class="btn btn-light rounded-pill px-4">
                Cancel
            </a>
            <button type="submit" class="btn btn-primary rounded-pill px-4 py-2.5 fw-bold shadow-sm d-inline-flex align-items-center gap-2" id="submitBtn" disabled>
                <i class="bi bi-send-check-fill"></i> Transmit to VPREI &amp; VPAF for Recommending Approval
            </button>
        </div>
    </form>

    <script>
        let rowIndex = 0;

        function addRow(data = {}) {
            const tbody = document.getElementById('itemsTableBody');
            const index = rowIndex++;

            const proposalId = data.proposal_id || '';
            const title = data.title || '';
            const proponents = data.proponents || '';
            const college = data.college || 'UA';
            const budget = data.budget !== undefined ? data.budget : '';

            const tr = document.createElement('tr');
            tr.id = `row-${index}`;
            tr.dataset.proposalId = proposalId;

            tr.innerHTML = `
                <td class="text-center fw-bold row-number"></td>
                <td>
                    <textarea name="items[${index}][project_title]" class="form-control rounded-2 text-sm" rows="2" placeholder="Enter complete project title" required>${escapeHtml(title)}</textarea>
                    ${proposalId ? `<input type="hidden" name="proposal_ids[]" value="${proposalId}"><input type="hidden" name="items[${index}][proposal_id]" value="${proposalId}">` : ''}
                </td>
                <td>
                    <textarea name="items[${index}][proponents]" class="form-control rounded-2 text-sm" rows="2" placeholder="e.g. Dr. Juan Dela Cruz, Prof. Maria Santos" required>${escapeHtml(proponents)}</textarea>
                </td>
                <td>
                    <input type="text" name="items[${index}][college]" class="form-control rounded-2 text-sm" value="${escapeHtml(college)}" placeholder="College / Dept">
                </td>
                <td>
                    <div class="input-group input-group-sm">
                        <span class="input-group-text">₱</span>
                        <input type="number" step="0.01" min="0" name="items[${index}][budget]" class="form-control budget-input rounded-end-2 text-end fw-bold" value="${budget}" oninput="recalcTotal()" required>
                    </div>
                </td>
                <td class="text-center">
                    <button type="button" class="btn btn-sm btn-light text-danger rounded-circle p-1" onclick="removeRow(${index}, '${proposalId}')" title="Remove row">
                        <i class="bi bi-trash"></i>
                    </button>
                </td>
            `;

            tbody.appendChild(tr);
            updateNumbers();
            recalcTotal();
        }

        function addCustomRow() {
            addRow({ title: '', proponents: '', college: 'College of Arts and Sciences', budget: 0 });
        }

        function removeRow(index, proposalId = '') {
            const row = document.getElementById(`row-${index}`);
            if (row) {
                row.remove();
            }

            if (proposalId) {
                const chk = document.querySelector(`.proposal-picker-chk[data-id="${proposalId}"]`);
                if (chk) chk.checked = false;
            }

            updateNumbers();
            recalcTotal();
        }

        function updateNumbers() {
            const rows = document.querySelectorAll('#itemsTableBody tr');
            rows.forEach((r, idx) => {
                const numCell = r.querySelector('.row-number');
                if (numCell) numCell.textContent = `${idx + 1}.`;
            });

            const hasItems = rows.length > 0;
            document.getElementById('noItemsAlert').classList.toggle('d-none', hasItems);
            document.getElementById('submitBtn').disabled = !hasItems;
        }

        function recalcTotal() {
            const inputs = document.querySelectorAll('.budget-input');
            let sum = 0;
            inputs.forEach(inp => {
                const val = parseFloat(inp.value);
                if (!isNaN(val)) sum += val;
            });

            document.getElementById('displayTotal').textContent = '₱' + sum.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        }

        function escapeHtml(text) {
            return String(text || '')
                .replace(/&/g, "&amp;")
                .replace(/</g, "&lt;")
                .replace(/>/g, "&gt;")
                .replace(/"/g, "&quot;")
                .replace(/'/g, "&#039;");
        }

        // Proposal Picker Checkbox handler
        document.querySelectorAll('.proposal-picker-chk').forEach(chk => {
            chk.addEventListener('change', function () {
                const propId = this.dataset.id;
                if (this.checked) {
                    addRow({
                        proposal_id: propId,
                        title: this.dataset.title,
                        proponents: this.dataset.proponents,
                        college: this.dataset.college,
                        budget: parseFloat(this.dataset.budget) || 0
                    });
                } else {
                    const existingRow = document.querySelector(`#itemsTableBody tr[data-proposal-id="${propId}"]`);
                    if (existingRow) existingRow.remove();
                    updateNumbers();
                    recalcTotal();
                }
            });
        });

        // Initialize with 1 default manual row if table is empty
        window.addEventListener('DOMContentLoaded', () => {
            updateNumbers();
        });
    </script>
</x-app-layout>
