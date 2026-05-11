<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: 'Nunito', sans-serif; background-color: #f8f9fa; color: #333; margin: 0; padding: 20px; }
        .container { max-width: 600px; margin: 0 auto; background-color: #ffffff; border-radius: 8px; overflow: hidden; box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
        .header { background-color: #0d6efd; color: #ffffff; padding: 20px; text-align: center; }
        .content { padding: 30px; line-height: 1.6; }
        .announcement-title { font-size: 24px; font-weight: bold; margin-bottom: 10px; color: #0d6efd; }
        .announcement-body { font-size: 16px; margin-bottom: 20px; white-space: pre-line; }
        .footer { background-color: #e9ecef; padding: 15px; text-align: center; font-size: 12px; color: #6c757d; }
        .btn { display: inline-block; padding: 10px 20px; background-color: #0d6efd; color: #ffffff; text-decoration: none; border-radius: 5px; font-weight: bold; margin-top: 10px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>URLC System</h1>
        </div>
        <div class="content">
            <p>Hello,</p>
            <p>A new announcement has been posted on the URLC Research Proposal Management System.</p>
            
            <div class="announcement-title">
                {{ $announcement->title }}
            </div>
            
            <div class="announcement-body">
                {{ $announcement->content }}
            </div>

            <a href="{{ url('/announcements') }}" class="btn">View on System</a>
            
            <p style="margin-top: 30px;">Best regards,<br>URLC Administration</p>
        </div>
        <div class="footer">
            &copy; {{ date('Y') }} URLC System. All rights reserved.
        </div>
    </div>
</body>
</html>
