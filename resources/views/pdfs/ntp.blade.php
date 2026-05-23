<!DOCTYPE html>
<html>
<head>
    <title>Notice to Proceed</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; margin: 40px; }
        .header { text-align: center; margin-bottom: 40px; }
        .title { font-size: 24px; font-weight: bold; margin-bottom: 10px; text-transform: uppercase; }
        .subtitle { font-size: 16px; color: #555; }
        .content { margin-bottom: 30px; }
        .field { margin-bottom: 10px; }
        .field-label { font-weight: bold; width: 150px; display: inline-block; }
        .footer { margin-top: 50px; }
        .signature { margin-top: 40px; border-top: 1px solid #000; width: 250px; text-align: center; padding-top: 5px; }
    </style>
</head>
<body>
    <div class="header">
        <div class="title">Notice to Proceed</div>
        <div class="subtitle">University Research Lifecycle Portal</div>
    </div>

    <div class="content">
        <p>Date: {{ date('F d, Y') }}</p>
        
        <p>To: <strong>{{ $proposal->user->name }}</strong><br>
        {{ $proposal->user->department }}</p>

        <p>Dear {{ $proposal->user->name }},</p>

        <p>We are pleased to inform you that your research proposal has been formally approved and authorized for implementation. You may now commence with your data gathering and other related research activities.</p>

        <div class="field">
            <span class="field-label">Proposal Code:</span> {{ $proposal->proposal_code ?? 'N/A' }}
        </div>
        <div class="field">
            <span class="field-label">Research Title:</span> {{ $proposal->title }}
        </div>
        <div class="field">
            <span class="field-label">Research Field:</span> {{ $proposal->research_field ?? 'N/A' }}
        </div>

        <p style="margin-top: 30px;">Please ensure that all activities are conducted in accordance with the approved proposal and institutional guidelines. Regular progress reports must be uploaded to the system as outlined in your approved timeline.</p>
    </div>

    <div class="footer">
        <p>Authorized by:</p>
        <div class="signature">
            <strong>Research Director / Admin</strong>
        </div>
    </div>
</body>
</html>
