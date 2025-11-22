<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile • Instagram</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background-color: #fafafa;
            color: #262626;
            line-height: 1.4;
        }

        .edit-profile-container {
            max-width: 935px;
            margin: 30px auto;
            background: white;
            border: 1px solid #dbdbdb;
            border-radius: 3px;
        }

        .edit-profile-header {
            padding: 16px;
            border-bottom: 1px solid #dbdbdb;
        }

        .edit-profile-title {
            font-size: 16px;
            font-weight: 600;
            text-align: center;
        }

        .edit-profile-content {
            display: flex;
            padding: 32px;
        }

        .profile-sidebar {
            width: 200px;
            margin-right: 50px;
            text-align: center;
        }

        .profile-avatar {
            width: 38px;
            height: 38px;
            border-radius: 50%;
            background: #dbdbdb;
            margin: 0 auto 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 16px;
            color: #8e8e8e;
        }

        .username {
            font-size: 20px;
            font-weight: 400;
            margin-bottom: 8px;
        }

        .change-photo-btn {
            background: transparent;
            border: none;
            color: #0095f6;
            font-weight: 600;
            font-size: 14px;
            cursor: pointer;
            padding: 0;
        }

        .edit-form {
            flex: 1;
            max-width: 400px;
        }

        .form-group {
            margin-bottom: 16px;
            display: flex;
            align-items: flex-start;
        }

        .form-label {
            width: 120px;
            padding-top: 7px;
            font-size: 16px;
            font-weight: 600;
            text-align: right;
            margin-right: 32px;
        }

        .form-input {
            flex: 1;
        }

        input, textarea {
            width: 100%;
            padding: 7px 12px;
            border: 1px solid #dbdbdb;
            border-radius: 3px;
            font-size: 16px;
            background: #fafafa;
        }

        input:focus, textarea:focus {
            outline: none;
            border-color: #a8a8a8;
        }

        textarea {
            resize: vertical;
            min-height: 80px;
            font-family: inherit;
        }

        .char-count {
            font-size: 12px;
            color: #8e8e8e;
            text-align: right;
            margin-top: 4px;
        }

        .website-note {
            background: #fafafa;
            border: 1px solid #dbdbdb;
            border-radius: 3px;
            padding: 16px;
            margin-top: 8px;
            font-size: 14px;
            color: #8e8e8e;
        }

        .website-note strong {
            display: block;
            margin-bottom: 4px;
            color: #262626;
        }

        .submit-section {
            margin-top: 24px;
            padding-left: 152px;
        }

        .submit-btn {
            background: #0095f6;
            color: white;
            border: none;
            border-radius: 4px;
            padding: 7px 16px;
            font-weight: 600;
            font-size: 14px;
            cursor: pointer;
        }

        .submit-btn:disabled {
            background: #b2dffc;
            cursor: default;
        }

        @media (max-width: 768px) {
            .edit-profile-container {
                margin: 0;
                border: none;
            }

            .edit-profile-content {
                flex-direction: column;
                padding: 16px;
            }

            .profile-sidebar {
                width: 100%;
                margin-right: 0;
                margin-bottom: 24px;
            }

            .form-group {
                flex-direction: column;
            }

            .form-label {
                width: 100%;
                text-align: left;
                margin-right: 0;
                margin-bottom: 8px;
            }

            .submit-section {
                padding-left: 0;
            }
        }
    </style>
</head>
<body>
<div class="edit-profile-container">
    <div class="edit-profile-header">
        <h1 class="edit-profile-title">Edit Profile</h1>
    </div>

    <div class="edit-profile-content">
        <div class="profile-sidebar">
            <div class="profile-avatar">
                👤
            </div>
            <div class="username">rayakhasati</div>
            <button class="change-photo-btn">Change photo</button>
        </div>

        <div class="edit-form">
            <!-- Username -->
            <div class="form-group">
                <label class="form-label">Username</label>
                <div class="form-input">
                    <input type="text" value="rayakhasati">
                </div>
            </div>

            <!-- Email -->
            <div class="form-group">
                <label class="form-label">Email</label>
                <div class="form-input">
                    <input type="email" value="raya@example.com">
                </div>
            </div>

            <!-- Phone -->
            <div class="form-group">
                <label class="form-label">Phone</label>
                <div class="form-input">
                    <input type="tel" value="+1 234 567 8900">
                </div>
            </div>

            <!-- Password -->
            <div class="form-group">
                <label class="form-label">Password</label>
                <div class="form-input">
                    <input type="password" value="••••••••">
                </div>
            </div>

            <!-- Website -->
            <div class="form-group">
                <label class="form-label">Website</label>
                <div class="form-input">
                    <input type="url" placeholder="Website">
                    <div class="website-note">
                        <strong>Editing your links is only available on mobile.</strong>
                        Visit the Instagram app and edit your profile to change the websites in your bio.
                    </div>
                </div>
            </div>

            <!-- Bio -->
            <div class="form-group">
                <label class="form-label">Bio</label>
                <div class="form-input">
                    <textarea placeholder="Bio">21110</textarea>
                    <div class="char-count">5 / 150</div>
                </div>
            </div>

            <!-- Submit Button -->
            <div class="form-group">
                <div class="form-label"></div>
                <div class="form-input submit-section">
                    <button class="submit-btn">Submit</button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Character counter for bio
    const bioTextarea = document.querySelector('textarea');
    const charCount = document.querySelector('.char-count');

    bioTextarea.addEventListener('input', function() {
        const count = this.value.length;
        charCount.textContent = `${count} / 150`;

        if (count > 150) {
            charCount.style.color = '#ed4956';
        } else {
            charCount.style.color = '#8e8e8e';
        }
    });

    // Trigger initial count
    bioTextarea.dispatchEvent(new Event('input'));
</script>
</body>
</html>