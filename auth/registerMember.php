<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Irish+Grover&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/registerMember.css">
</head>

<body>
    <!-- main content -->
    <div class="main-content">
        <!-- form-content -->
        <form class="form-content" id="form" action="" method="POST" enctype="multipart/form-data">
            <!-- rigester-title with dimonds and lines -->
            <h1 class="register-title">Register</h1>
            <div class="divider">
                <span class="line"></span>
                <span class="diamond"></span>
                <span class="line"></span>
            </div>
            <!-- inputs -->
            <div class="input-group">
                <label for="name">Full Name</label>
                <input type="text" id="name" name="name" placeholder="Enter your full name">
            </div>

            <div class="input-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" placeholder="Enter your email">
            </div>

            <div class="input-group">
                <label for="phone">Phone</label>
                <input type="text" id="phone" name="phone" placeholder="Enter phone number">
            </div>

            <div class="input-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" placeholder="Enter password">
            </div>

            <div class="input-group">
                <label for="workshop">Workshop</label>
                <select id="workshop" name="workshop">
                    <option value="">Select Workshop</option>
                </select>
            </div>

            <div class="input-group">
                <label for="roleID">Member in</label>
                <select id="roleID" name="roleID">
                    <option value="">hr</option>
                    <option value="">acs</option>
                    <option value="2">it</option>
                    <option value="3">dd</option>
                    <option value="3">mb</option>
                    <option value="3">smm</option>
                    <option value="3">bd</option>
                    <option value="3">cr</option>
                    <option value="3">br</option>
                    <option value="3">logistes</option>
                </select>
            </div>

            <div class="input-group">
                <label for="github">GitHub</label>
                <input type="text" id="github" name="github" placeholder="Github link">
            </div>

            <div class="input-group">
                <label for="linkedin">LinkedIn</label>
                <input type="text" id="linkedin" name="linkedin" placeholder="LinkedIn link">
            </div>

            <div class="input-group">
                <label for="image">Image</label>
                <input type="file" id="image" name="image" accept="image/*">
            </div>

            <!-- submit-button -->
            <button class="submit-btn" type="submit" name="submit">Register</button>

        </form>
    </div>

    <script src="../assets/js/registerParticipant.js"></script>
</body>

</html>
