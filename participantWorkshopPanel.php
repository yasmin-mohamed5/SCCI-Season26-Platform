<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- google font -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <!-- Irish Grover font -->
    <link href="https://fonts.googleapis.com/css2?family=Irish+Grover&display=swap" rel="stylesheet" />

    <!-- site icon -->
    <link rel="icon" type="image/png" href="./assets/icons/logoSCCI.png" />

    <!-- css other link -->
    <link rel="stylesheet" href="./assets/css/all.min.css">
    <link rel="stylesheet" href="./assets/css/root.css">

    <!-- css page link -->
    <link rel="stylesheet" href="./assets/css/participantWorkshopPanel.css">

    <!-- Page Title -->
    <title>SCCI - Workshop Panel</title>
</head>

<body>
    <?php include './includes/nav.php'; ?>
<!-- panel ----------------------------------------------------------------- -->

    <div class="navbar-spacer"></div>
     
        <div class="miniNav">
            <div class="panelSvg">
                <!-- left edge -->
                <svg
                    shape-rendering="geometricPrecision"
                    class="panelEdge"
                    preserveAspectRatio="none"
                    viewBox="0 0 50 100"
                    xmlns="http://www.w3.org/2000/svg"
                    aria-hidden="true">
                    <path
                        d="M50 0
                        C40 0 30 20 10 50
                        C30 80 40 100 50 100
                        Z"
                        fill="var(--color-primary-darker)"
                        stroke="var(--color-primary-darker)"
                        stroke-width="2"
                        stroke-linejoin="round"
                        stroke-linecap="round" />
                </svg>

                <!-- center -->
                <svg
                    shape-rendering="geometricPrecision"
                    class="panelBody"
                    viewBox="0 0 300 100"
                    preserveAspectRatio="none"
                    xmlns="http://www.w3.org/2000/svg"
                    aria-hidden="true">
                    <defs>
                        <linearGradient id="fillCenter" x1="0%" y1="0%" x2="100%" y2="0%">
                            <stop offset="0%" stop-color="var(--color-primary-darker)" />
                            <stop offset="50%" stop-color="var(--color-primary)" />
                            <stop offset="100%" stop-color="var(--color-primary-darker)" />
                        </linearGradient>
                    </defs>

                    <rect
                        x="0"
                        y="0"
                        width="300"
                        height="100"
                        fill="url(#fillCenter)"
                        stroke="var(--color-primary-darker)"
                        stroke-width="2" />
                </svg>

                <!-- right edge -->
                <svg
                    shape-rendering="geometricPrecision"
                    class="panelEdge"
                    preserveAspectRatio="none"
                    viewBox="0 0 50 100"
                    xmlns="http://www.w3.org/2000/svg"
                    aria-hidden="true">
                    <path
                        d="M0 0
                        C10 0 20 20 40 50
                        C20 80 10 100 0 100
                        Z"
                        fill="var(--color-primary-darker)"
                        stroke="var(--color-primary-darker)"
                        stroke-width="2"
                        stroke-linejoin="round"
                        stroke-linecap="round" />
                </svg>
            </div>

            <!-- Name the "data-page" in the mini nav the same as its section -->
            <a data-page="evaluate" class="activePanelLine">view task</a>
            <a data-page="review" class="">review Task</a>
            <a data-page="addTask" class="">materials</a>
            <a data-page="addMaterial" class="">activity time </a>
        </div>
        
    <!-- Main Workshop Panel Section -->
    <section class="workshopPanelSection">
        <div class="container">

            <!-- Week Navigation Tabs -->
            <!-- Week Navigation Tabs: Allows switching between workshop weeks -->
            <div class="workshopNav">
                <button class="weekTab active" data-week="1">session 1</button>
                <button class="weekTab" data-week="2">session 2</button>
                <button class="weekTab" data-week="3">session 3</button>
                <button class="weekTab" data-week="4">session 4</button>
                <button class="weekTab" data-week="5">session 5</button>
                <button class="weekTab" data-week="6">session6</button>
                <button class="weekTab" data-week="7">session 7</button>
            </div>

            <!-- Workshop Card -->
            <article class="workshopCard">
                <!-- Card Header -->
                <header class="cardHeader">
                    <h2>WEEKLY TASK</h2>
                </header>

        <!-- Card Body -->
        <div class="cardBody">
            <!-- Task Name Row -->
            <div class="taskRow">
                <label class="taskLabel">Task Name:</label>
                <div class="taskValue">build your first website</div>
            </div>

            <!-- Deadline Row -->
            <div class="taskRow">
                <label class="taskLabel">Deadline:</label>
                <div class="taskValue">12/12/2025</div>
            </div>

            <!-- Session Row -->
            <div class="taskRow">
                <label class="taskLabel">Session:</label>
                <div class="taskValue">1</div>
            </div>

            <!-- Task Bio Box -->
            <div class="taskBio">
                <label class="taskLabel">Task bio:</label>
                <div class="taskBioContent">
                    Your required to use semantic elements and make the page responsive with clean folder
                    structure
                </div>
            </div>

            <!-- Task Resource Row -->
            <div class="taskResource">
                <label class="taskLabel">Task Resource:</label>
                <div class="resourceInfo">
                    <i class="fas fa-file-alt"></i>
                    <span>task.HTML</span>
                    <a href="#" class="downloadBtn">
                        <i class="fas fa-download"></i>
                        Download
                    </a>
                </div>
            </div>
        </div>
            </article>
        <!-- Submit Task Form Section -->
        <form class="fileUpload" id="validForm" action="" method="post" enctype="multipart/form-data">
            <!-- Upload Header -->
            <div class="uploadHeader">
                <h3 class="uploadSectionTitle">Submit Task</h3>
            </div>
            <!-- Upload Container -->
            <div class="uploadContainer" id="taskUploadContainer">
                <label class="formLabel" for="taskFile">
                    <div class="uploadIcon">
                        <i class="fas fa-arrow-down"></i>
                    </div>
                    <h4 class="uploadTitle">Upload File</h4>
                    <p class="uploadText" id="fileUploadState">
                        Drag and drop or click to browse
                    </p>
                </label>

                <p id="fileUploadedName"></p>
                <!-- Hidden File Input -->
                <input type="file" name="taskFile" id="taskFile">

                <p id="fileMessage"></p>
            </div>

        </form>

        </div>
    </section>

    <!-- Footer Section -->
    <?php include './includes/footer.php'; ?>

    <!-- Page Scripts -->
    <script src="./assets/js/all.min.js" defer></script>
    <script src="./assets/js/participantWorkshopPanel.js" defer></script>
</body>

</html>