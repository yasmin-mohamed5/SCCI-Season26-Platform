// get nav links
const participantBtn = document.querySelector('.participant');
const memberBtn = document.querySelector('.member');

// get table sections
const participantsSchedule = document.getElementById('participantsSchedule');
const membersSchedule = document.getElementById('membersSchedule');

// get current section from URL parameter
const urlParams = new URLSearchParams(window.location.search);
const currentSection = urlParams.get('section') || 'participants';


// show participants
participantBtn.addEventListener('click', () => {
    participantsSchedule.style.display = 'block';
    membersSchedule.style.display = 'none';
    participantBtn.classList.add('activePanelLine');
    memberBtn.classList.remove('activePanelLine');
    // Update URL without reload
    window.history.replaceState({}, '', 'headPanel.php?section=participants');
});

// show members
memberBtn.addEventListener('click', () => {
    membersSchedule.style.display = 'block';
    participantsSchedule.style.display = 'none';
    memberBtn.classList.add('activePanelLine');
    participantBtn.classList.remove('activePanelLine');
    // Update URL without reload
    window.history.replaceState({}, '', 'headPanel.php?section=members');
});

// Set initial active state based on URL parameter
if (currentSection === 'members') {
    membersSchedule.style.display = 'block';
    participantsSchedule.style.display = 'none';
    memberBtn.classList.add('activePanelLine');
    participantBtn.classList.remove('activePanelLine');
} else {
    participantBtn.classList.add('activePanelLine');
}
