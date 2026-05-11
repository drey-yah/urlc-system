<x-app-layout>
    <div class="mb-5">
        <h1 class="h3 fw-bold d-flex align-items-center gap-3 mb-2">
            <i class="bi bi-envelope text-primary"></i> Email Notification Templates
        </h1>
        <p class="text-muted mb-0">Premium HTML email templates for automated researcher notifications</p>
    </div>

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body p-4">
            <h5 class="fw-bold mb-4">Select Template Type</h5>
            <div class="d-flex gap-3">
                <button class="btn btn-primary px-4">Approval Email</button>
                <button class="btn btn-outline-secondary px-4">Rejection Email</button>
                <button class="btn btn-outline-secondary px-4">Under Review Email</button>
            </div>
        </div>
    </div>

    <!-- Email Preview -->
    <div class="bg-light p-4 p-lg-5 rounded-4 shadow-sm">
        <div class="mx-auto" style="max-width: 600px;">
            <div class="card border-0 shadow-lg">
                <!-- Email Header -->
                <div class="p-4 text-center" style="background-color: #10B981; color: white;">
                    <div class="bg-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 64px; height: 64px;">
                        <i class="bi bi-file-earmark-text-fill text-success h2 mb-0"></i>
                    </div>
                    <h2 class="h4 fw-bold mb-1">URLC Research Portal</h2>
                    <p class="small mb-0 opacity-90">Research Proposal Management System</p>
                </div>
                
                <div class="card-body p-4 p-lg-5">
                    <h3 class="h4 fw-bold text-dark mb-4 text-center">Congratulations! Your Proposal Has Been Approved</h3>
                    <p class="text-muted">Dear Dr. Sarah Johnson,</p>
                    <p class="text-muted">We are pleased to inform you that your research proposal has been approved by our review committee.</p>
                    
                    <div class="bg-light p-3 rounded-3 border-start border-primary border-4 mb-4">
                        <small class="text-muted fw-bold text-uppercase d-block mb-1">Proposal Title:</small>
                        <p class="fw-bold mb-0 text-primary">Machine Learning Applications in Climate Research</p>
                    </div>

                    <div class="bg-light p-3 rounded-3 mb-4">
                        <small class="text-muted fw-bold text-uppercase d-block mb-1">Reviewer Comments:</small>
                        <p class="text-muted small mb-0">Excellent methodology and clear objectives. Your research shows great promise.</p>
                    </div>

                    <div class="text-center mt-5">
                        <a href="#" class="btn btn-primary btn-lg px-5">View Proposal Details</a>
                    </div>
                </div>

                <div class="card-footer bg-white border-0 text-center p-4">
                    <p class="small text-muted mb-4">If you have any questions, please contact our support team at <a href="mailto:support@urlc.edu">support@urlc.edu</a></p>
                    <hr>
                    <p class="small text-muted mt-4 opacity-50">&copy; 2026 URLC Research Portal • Powered by Cloud Infrastructure<br>This is an automated email notification. Please do not reply to this email.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Features Card -->
    <div class="card border-0 shadow-sm mt-5 bg-primary bg-opacity-10">
        <div class="card-body p-4">
            <h5 class="fw-bold text-primary mb-3">Email System Features</h5>
            <div class="row g-4">
                <div class="col-md-3">
                    <div class="d-flex gap-3">
                        <i class="bi bi-lightning-fill text-primary"></i>
                        <div>
                            <p class="small mb-0"><strong class="text-dark">Automated Delivery:</strong> Emails are sent automatically when proposal status changes</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="d-flex gap-3">
                        <i class="bi bi-brush-fill text-primary"></i>
                        <div>
                            <p class="small mb-0"><strong class="text-dark">Branded Design:</strong> Professional HTML templates with URLC branding</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="d-flex gap-3">
                        <i class="bi bi-link-45deg text-primary"></i>
                        <div>
                            <p class="small mb-0"><strong class="text-dark">Call-to-Action:</strong> Direct links to view proposal details in the portal</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="d-flex gap-3">
                        <i class="bi bi-phone-fill text-primary"></i>
                        <div>
                            <p class="small mb-0"><strong class="text-dark">Responsive Design:</strong> Optimized for desktop and mobile email clients</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
