<style>
    .grading-help-tour__launcher {
        position: fixed;
        right: 1.25rem;
        bottom: 1.25rem;
        z-index: 90;
        display: inline-flex;
        align-items: center;
        gap: .55rem;
        min-height: 3.25rem;
        padding: .7rem 1rem;
        color: #fff;
        font-size: .875rem;
        font-weight: 700;
        letter-spacing: .01em;
        background: linear-gradient(135deg, #d97706, #f59e0b);
        border: 1px solid rgba(255, 255, 255, .42);
        border-radius: 999px;
        box-shadow: 0 12px 28px rgba(120, 53, 15, .24), 0 0 0 4px rgba(245, 158, 11, .12);
        cursor: pointer;
        transition: transform .2s ease, box-shadow .2s ease;
    }

    .grading-help-tour__launcher:hover {
        transform: translateY(-2px);
        box-shadow: 0 16px 32px rgba(120, 53, 15, .3), 0 0 0 5px rgba(245, 158, 11, .16);
    }

    .grading-help-tour__launcher:focus-visible,
    .grading-help-tour__button:focus-visible,
    .grading-help-tour__close:focus-visible {
        outline: 3px solid rgba(245, 158, 11, .5);
        outline-offset: 3px;
    }

    .grading-help-tour__launcher-icon {
        display: grid;
        width: 1.55rem;
        height: 1.55rem;
        place-items: center;
        color: #92400e;
        background: #fff7ed;
        border-radius: 50%;
    }

    .grading-help-tour[hidden],
    .grading-help-tour__spotlight[hidden] {
        display: none !important;
    }

    .grading-help-tour__backdrop {
        position: fixed;
        inset: 0;
        z-index: 100;
        background: rgba(15, 23, 42, .58);
        animation: grading-help-tour-fade-in .18s ease-out;
    }

    .grading-help-tour__spotlight {
        position: fixed;
        z-index: 101;
        pointer-events: none;
        border: 2px solid rgba(251, 191, 36, .98);
        border-radius: .8rem;
        box-shadow: 0 0 0 9999px rgba(15, 23, 42, .58), 0 0 0 5px rgba(251, 191, 36, .18), 0 0 24px rgba(251, 191, 36, .48);
        transition: top .22s ease, left .22s ease, width .22s ease, height .22s ease;
    }

    .grading-help-tour__card {
        position: fixed;
        z-index: 102;
        width: min(22rem, calc(100vw - 2rem));
        max-height: calc(100vh - 2rem);
        overflow: auto;
        padding: 1.15rem;
        color: #1e293b;
        text-align: left;
        background: #fff;
        border: 1px solid rgba(226, 232, 240, .9);
        border-radius: 1rem;
        box-shadow: 0 24px 60px rgba(15, 23, 42, .28);
        animation: grading-help-tour-card-in .22s ease-out;
    }

    .dark .grading-help-tour__card {
        color: #e2e8f0;
        background: #1e293b;
        border-color: rgba(71, 85, 105, .9);
    }

    .grading-help-tour__eyebrow,
    .grading-help-tour__actions {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: .75rem;
    }

    .grading-help-tour__eyebrow {
        margin-bottom: .75rem;
        color: #b45309;
        font-size: .7rem;
        font-weight: 800;
        letter-spacing: .1em;
        text-transform: uppercase;
    }

    .dark .grading-help-tour__eyebrow {
        color: #fbbf24;
    }

    .grading-help-tour__progress {
        color: #94a3b8;
        font-variant-numeric: tabular-nums;
        letter-spacing: .04em;
    }

    .grading-help-tour__heading {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: .75rem;
    }

    .grading-help-tour__title {
        margin: 0;
        font-size: 1.05rem;
        font-weight: 800;
        line-height: 1.3;
    }

    .grading-help-tour__close {
        display: inline-grid;
        flex: 0 0 auto;
        width: 2rem;
        height: 2rem;
        margin: -.2rem -.25rem 0 0;
        color: #64748b;
        place-items: center;
        border-radius: .5rem;
        cursor: pointer;
    }

    .grading-help-tour__close:hover {
        color: #0f172a;
        background: #f1f5f9;
    }

    .dark .grading-help-tour__close:hover {
        color: #f8fafc;
        background: #334155;
    }

    .grading-help-tour__description {
        margin: .7rem 0 1.15rem;
        color: #64748b;
        font-size: .875rem;
        line-height: 1.55;
    }

    .dark .grading-help-tour__description {
        color: #cbd5e1;
    }

    .grading-help-tour__skip {
        padding: .45rem 0;
        color: #64748b;
        font-size: .8rem;
        font-weight: 600;
        cursor: pointer;
    }

    .grading-help-tour__skip:hover {
        color: #334155;
        text-decoration: underline;
    }

    .dark .grading-help-tour__skip:hover {
        color: #f1f5f9;
    }

    .grading-help-tour__button {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: .4rem;
        min-width: 5.75rem;
        padding: .6rem .85rem;
        color: #fff;
        font-size: .8rem;
        font-weight: 750;
        background: #d97706;
        border-radius: .6rem;
        cursor: pointer;
    }

    .grading-help-tour__button:hover {
        background: #b45309;
    }

    .grading-help-tour__button--secondary {
        color: #475569;
        background: #f1f5f9;
    }

    .grading-help-tour__button--secondary:hover {
        background: #e2e8f0;
    }

    .dark .grading-help-tour__button--secondary {
        color: #e2e8f0;
        background: #334155;
    }

    .dark .grading-help-tour__button--secondary:hover {
        background: #475569;
    }

    @keyframes grading-help-tour-fade-in {
        from { opacity: 0; }
        to { opacity: 1; }
    }

    @keyframes grading-help-tour-card-in {
        from { opacity: 0; transform: translateY(6px) scale(.98); }
        to { opacity: 1; transform: translateY(0) scale(1); }
    }

    @media (max-width: 640px) {
        .grading-help-tour__launcher {
            right: 1rem;
            bottom: 1rem;
            width: 3.25rem;
            height: 3.25rem;
            padding: 0;
            justify-content: center;
        }

        .grading-help-tour__launcher-label {
            display: none;
        }
    }

    @media (prefers-reduced-motion: reduce) {
        .grading-help-tour__launcher,
        .grading-help-tour__spotlight,
        .grading-help-tour__backdrop,
        .grading-help-tour__card {
            animation: none;
            transition: none;
        }
    }
</style>

<button type="button" class="grading-help-tour__launcher" id="grading-help-tour-launcher"
    aria-label="Open page tutorial" aria-controls="grading-help-tour">
    <span class="grading-help-tour__launcher-icon" aria-hidden="true">
        <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
            <circle cx="12" cy="12" r="9"></circle>
            <path d="M9.6 9a2.5 2.5 0 1 1 4.3 1.7c-1 .9-1.9 1.4-1.9 2.8"></path>
            <path d="M12 17h.01"></path>
        </svg>
    </span>
    <span class="grading-help-tour__launcher-label">How to use</span>
</button>

<div class="grading-help-tour" id="grading-help-tour" hidden>
    <div class="grading-help-tour__backdrop" data-tour-backdrop></div>
    <div class="grading-help-tour__spotlight" data-tour-spotlight hidden></div>

    <section class="grading-help-tour__card" data-tour-card role="dialog" aria-modal="true"
        aria-labelledby="grading-help-tour-title" aria-describedby="grading-help-tour-description" hidden>
        <div class="grading-help-tour__eyebrow">
            <span>Quick tour</span>
            <span class="grading-help-tour__progress" data-tour-progress></span>
        </div>
        <div class="grading-help-tour__heading">
            <h2 class="grading-help-tour__title" id="grading-help-tour-title" data-tour-title></h2>
            <button type="button" class="grading-help-tour__close" data-tour-close aria-label="Close tutorial">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
                    <path d="M6 6l12 12M18 6L6 18"></path>
                </svg>
            </button>
        </div>
        <p class="grading-help-tour__description" id="grading-help-tour-description" data-tour-description></p>
        <div class="grading-help-tour__actions">
            <button type="button" class="grading-help-tour__skip" data-tour-skip>Skip tour</button>
            <div style="display: flex; gap: .5rem;">
                <button type="button" class="grading-help-tour__button grading-help-tour__button--secondary" data-tour-previous hidden>Back</button>
                <button type="button" class="grading-help-tour__button" data-tour-next>Next</button>
            </div>
        </div>
    </section>
</div>

<script>
    (() => {
        const launcher = document.getElementById('grading-help-tour-launcher');
        const tour = document.getElementById('grading-help-tour');

        if (!launcher || !tour || tour.dataset.initialized === 'true') {
            return;
        }

        tour.dataset.initialized = 'true';

        const card = tour.querySelector('[data-tour-card]');
        const spotlight = tour.querySelector('[data-tour-spotlight]');
        const title = tour.querySelector('[data-tour-title]');
        const description = tour.querySelector('[data-tour-description]');
        const progress = tour.querySelector('[data-tour-progress]');
        const nextButton = tour.querySelector('[data-tour-next]');
        const previousButton = tour.querySelector('[data-tour-previous]');
        const closeButton = tour.querySelector('[data-tour-close]');
        const skipButton = tour.querySelector('[data-tour-skip]');
        const backdrop = tour.querySelector('[data-tour-backdrop]');
        let steps = [];
        let currentStep = 0;
        let lastFocusedElement = null;
        let previousBodyOverflow = '';

        const sharedTourSteps = [
            {
                title: 'Navigate from the sidebar',
                description: 'Use the sidebar to switch between the dashboard, enrollments, grades, reports, and other parts of the grading system.',
                selectors: ['.fi-sidebar', 'aside.fi-sidebar', 'nav.fi-sidebar'],
            },
            {
                title: 'Check the current page',
                description: 'The page heading tells you where you are. Page-specific instructions, filters, and actions are usually organized around this area.',
                selectors: ['.fi-header-heading', '.fi-page-header-heading', 'main h1'],
            },
            {
                title: 'Use page actions',
                description: 'Look for buttons such as Add, Import, Export, or Generate near the page heading. The available actions change with the page you are using.',
                selectors: ['.fi-header-actions-ctn', '.fi-header-actions-ctn .fi-btn', '.fi-page-header-actions .fi-btn', '.fi-header-actions .fi-btn'],
            },
            {
                title: 'Search and filter tables',
                description: 'On list pages, use the search box and filters to narrow the records you need before editing or reviewing them.',
                selectors: ['.fi-ta-filters-trigger-action-ctn', '.fi-ta-search-field', '.fi-ta-header-toolbar', 'input[placeholder*="Search" i]', '.fi-ta-filter-indicators'],
            },
            {
                title: 'Open your account menu',
                description: 'Your account menu is where you can edit your profile or log out when you are finished.',
                selectors: ['.fi-user-menu', 'button[aria-label*="user" i]', 'button[aria-label*="account" i]', '.fi-topbar .fi-icon-btn:last-child'],
            },
        ];

        const reportsTourSteps = [
            {
                title: 'Navigate from the sidebar',
                description: 'Use the sidebar to move between the dashboard, report cards, grades, enrollments, and the other parts of the grading system.',
                selectors: ['.fi-sidebar', 'aside.fi-sidebar', 'nav.fi-sidebar'],
            },
            {
                title: 'Review the Reports page',
                description: 'This page lists active students for the current school year. Use the controls below the heading to prepare the report you need.',
                selectors: ['.fi-header-heading', '.fi-page-header-heading', 'main h1'],
            },
            {
                title: 'Filter by grade level',
                description: 'Open the Grade Level filter to narrow the student list. The selected grade level is also used when you generate the batch Report Card.',
                selectors: ['.fi-ta-filters-trigger-action-ctn', '.fi-ta-filters-above-content-ctn', '.fi-ta-filters-dropdown', '.fi-ta-filters-modal'],
            },
            {
                title: 'Use the main Report Card action',
                description: 'The main action beside the Reports heading generates report cards for the filtered list. Set your grade-level filter first when you need a specific group.',
                selectors: ['.fi-header-actions-ctn', '.fi-header-actions-ctn .fi-btn'],
            },
            {
                title: 'Open an individual student report',
                description: 'Each student row has Report Card and Award Card actions. Use these when you need a single student’s report instead of the batch report.',
                selectors: ['.fi-ta-record .fi-ta-actions', '.fi-ta-row .fi-ta-actions'],
            },
            {
                title: 'Search the student list',
                description: 'Search by student name when you already know who you need. Search works together with the grade-level filter.',
                selectors: ['.fi-ta-search-field', 'input[placeholder*="Search" i]'],
            },
            {
                title: 'Open your account menu',
                description: 'Your account menu is where you can edit your profile or log out when you are finished.',
                selectors: ['.fi-user-menu', 'button[aria-label*="user" i]', 'button[aria-label*="account" i]', '.fi-topbar .fi-icon-btn:last-child'],
            },
        ];

        const studentsTourSteps = [
            {
                title: 'Navigate from the sidebar',
                description: 'Use the sidebar to move between the student list, enrollments, grades, reports, and the other parts of the grading system.',
                selectors: ['.fi-sidebar', 'aside.fi-sidebar', 'nav.fi-sidebar'],
            },
            {
                title: 'Review the Students page',
                description: 'This page contains the student directory. Search, filter, and open a student record from the table below.',
                selectors: ['.fi-header-heading', '.fi-page-header-heading', 'main h1'],
            },
            {
                title: 'Add a new student',
                description: 'Use the main action beside the Students heading to create a new student record.',
                selectors: ['.fi-header-actions-ctn', '.fi-header-actions-ctn .fi-btn'],
            },
            {
                title: 'Filter student records',
                description: 'Use the filters to narrow the list by active status or gender before reviewing records.',
                selectors: ['.fi-ta-filters-trigger-action-ctn', '.fi-ta-filters-above-content-ctn'],
            },
            {
                title: 'Search for a student',
                description: 'Search by student ID, first name, middle name, or last name to find a record quickly.',
                selectors: ['.fi-ta-search-field', 'input[placeholder*="Search" i]'],
            },
            {
                title: 'Open a student record',
                description: 'Use the View action in a student row to review or edit that student’s details.',
                selectors: ['.fi-ta-record .fi-ta-actions', '.fi-ta-row .fi-ta-actions'],
            },
            {
                title: 'Export selected students',
                description: 'Select records first, then use the table toolbar to export them or apply a bulk action.',
                selectors: ['.fi-ta-header-toolbar', '.fi-ta-selection-indicator-actions-ctn'],
            },
            {
                title: 'Open your account menu',
                description: 'Your account menu is where you can edit your profile or log out when you are finished.',
                selectors: ['.fi-user-menu', 'button[aria-label*="user" i]', 'button[aria-label*="account" i]', '.fi-topbar .fi-icon-btn:last-child'],
            },
        ];

        const subjectTeachersTourSteps = [
            {
                title: 'Navigate from the sidebar',
                description: 'Use the sidebar to move between subject assignments, students, grades, reports, and the other parts of the grading system.',
                selectors: ['.fi-sidebar', 'aside.fi-sidebar', 'nav.fi-sidebar'],
            },
            {
                title: 'Review the Subject Teacher page',
                description: 'This page lists the active subjects for the current school year and lets you assign teachers and units inline.',
                selectors: ['.fi-header-heading', '.fi-page-header-heading', 'main h1'],
            },
            {
                title: 'Save subject assignments',
                description: 'After changing a teacher or unit value in the table, use Save All Changes in the main header actions to commit your updates.',
                selectors: ['.fi-header-actions-ctn', '.fi-header-actions-ctn .fi-btn'],
            },
            {
                title: 'Filter by grade level',
                description: 'Use the Grade Level filter to focus on subjects for a specific grade before assigning teachers.',
                selectors: ['.fi-ta-filters-trigger-action-ctn', '.fi-ta-filters-above-content-ctn'],
            },
            {
                title: 'Assign teachers and units',
                description: 'Edit the Teacher and Units cells directly in the table. Your changes remain pending until you save them.',
                selectors: ['.fi-ta-record .fi-ta-select', '.fi-ta-record .fi-ta-input', '.fi-ta-row .fi-ta-select', '.fi-ta-row .fi-ta-input', '.fi-ta-record select', '.fi-ta-record input'],
            },
            {
                title: 'Search for a subject',
                description: 'Search the subject title when you already know which subject assignment you need to update.',
                selectors: ['.fi-ta-search-field', 'input[placeholder*="Search" i]'],
            },
            {
                title: 'Open your account menu',
                description: 'Your account menu is where you can edit your profile or log out when you are finished.',
                selectors: ['.fi-user-menu', 'button[aria-label*="user" i]', 'button[aria-label*="account" i]', '.fi-topbar .fi-icon-btn:last-child'],
            },
        ];

        const homeroomsTourSteps = [
            {
                title: 'Navigate from the sidebar',
                description: 'Use the sidebar to move between homerooms, attendance, students, grades, and the other parts of the grading system.',
                selectors: ['.fi-sidebar', 'aside.fi-sidebar', 'nav.fi-sidebar'],
            },
            {
                title: 'Review the Homerooms page',
                description: 'This page shows the homerooms for the current school year, including their grade level, section, adviser, and co-adviser.',
                selectors: ['.fi-header-heading', '.fi-page-header-heading', 'main h1'],
            },
            {
                title: 'Update adviser assignments',
                description: 'Use the editable Adviser and Co-Adviser cells in each row to assign or update homeroom teachers.',
                selectors: ['.fi-ta-record .fi-ta-select', '.fi-ta-record .fi-ta-input', '.fi-ta-row .fi-ta-select', '.fi-ta-row .fi-ta-input', '.fi-ta-record select', '.fi-ta-record input'],
            },
            {
                title: 'Use the homeroom table',
                description: 'Review the grade level and section columns to confirm that each adviser assignment is attached to the right homeroom.',
                selectors: ['.fi-ta-content', '.fi-ta-table', '.fi-ta-records'],
            },
            {
                title: 'Open your account menu',
                description: 'Your account menu is where you can edit your profile or log out when you are finished.',
                selectors: ['.fi-user-menu', 'button[aria-label*="user" i]', 'button[aria-label*="account" i]', '.fi-topbar .fi-icon-btn:last-child'],
            },
        ];

        const enrollmentsTourSteps = [
            {
                title: 'Navigate from the sidebar',
                description: 'Use the sidebar to move between enrollments, students, grades, reports, and the other parts of the grading system.',
                selectors: ['.fi-sidebar', 'aside.fi-sidebar', 'nav.fi-sidebar'],
            },
            {
                title: 'Review the Enrollments page',
                description: 'This page lists active students and their current enrollment status for the selected school year.',
                selectors: ['.fi-header-heading', '.fi-page-header-heading', 'main h1'],
            },
            {
                title: 'Filter enrollment status',
                description: 'Open the enrollment filter to choose Enrolled or Not Enrolled, then narrow the results by school year or grade level.',
                selectors: ['.fi-ta-filters-trigger-action-ctn', '.fi-ta-filters-above-content-ctn'],
            },
            {
                title: 'Search for a student',
                description: 'Search by student ID or name to locate the record you need to enroll or review.',
                selectors: ['.fi-ta-search-field', 'input[placeholder*="Search" i]'],
            },
            {
                title: 'Select students for a bulk action',
                description: 'Use the row checkboxes to select students. Once records are selected, bulk enrollment actions appear in the table toolbar.',
                selectors: ['.fi-ta-record-checkbox', '.fi-ta-selection-indicator'],
            },
            {
                title: 'Use enrollment bulk actions',
                description: 'After selecting students and choosing the right filter, use the toolbar to enroll or deactivate the selected students.',
                selectors: ['.fi-ta-header-toolbar', '.fi-ta-selection-indicator-actions-ctn'],
            },
            {
                title: 'View a student record',
                description: 'Use View Student in a row when you need to open the full student record instead of changing enrollment in bulk.',
                selectors: ['.fi-ta-record .fi-ta-actions', '.fi-ta-row .fi-ta-actions'],
            },
            {
                title: 'Open your account menu',
                description: 'Your account menu is where you can edit your profile or log out when you are finished.',
                selectors: ['.fi-user-menu', 'button[aria-label*="user" i]', 'button[aria-label*="account" i]', '.fi-topbar .fi-icon-btn:last-child'],
            },
        ];

        const getPageContext = () => {
            const heading = document.querySelector('.fi-header-heading, .fi-page-header-heading, main h1')?.textContent ?? '';
            const path = window.location.pathname.toLowerCase();

            if (/\/reports(?:\/|$)/i.test(path) || /\breports\b/i.test(heading)) {
                return 'reports';
            }

            if (path.includes('/subject_teacher') || /subject teacher/i.test(heading)) {
                return 'subjectTeachers';
            }

            if (path.includes('/students') || /^\s*students\b/i.test(heading)) {
                return 'students';
            }

            if (path.includes('/homerooms') || /homerooms/i.test(heading)) {
                return 'homerooms';
            }

            if (path.includes('/enrollments') || /enrollment/i.test(heading)) {
                return 'enrollments';
            }

            return 'shared';
        };

        const getTourSteps = () => ({
            reports: reportsTourSteps,
            students: studentsTourSteps,
            subjectTeachers: subjectTeachersTourSteps,
            homerooms: homeroomsTourSteps,
            enrollments: enrollmentsTourSteps,
            shared: sharedTourSteps,
        })[getPageContext()];

        const isVisible = (element) => {
            if (!element) {
                return false;
            }

            const rect = element.getBoundingClientRect();
            const style = window.getComputedStyle(element);

            return rect.width > 0 && rect.height > 0 && style.visibility !== 'hidden' && style.display !== 'none';
        };

        const findTarget = (selectors) => selectors
            .map((selector) => document.querySelector(selector))
            .find(isVisible);

        const collectSteps = () => getTourSteps()
            .map((step) => ({ ...step, target: findTarget(step.selectors) }))
            .filter((step) => step.target);

        const positionCard = (target) => {
            if (!target) {
                spotlight.hidden = true;
                card.style.left = '50%';
                card.style.top = '50%';
                card.style.transform = 'translate(-50%, -50%)';
                return;
            }

            const padding = 8;
            const rect = target.getBoundingClientRect();
            const cardRect = card.getBoundingClientRect();
            const gap = 18;
            const viewportPadding = 16;
            const canPlaceBelow = rect.bottom + gap + cardRect.height <= window.innerHeight - viewportPadding;
            const top = canPlaceBelow ? rect.bottom + gap : Math.max(viewportPadding, rect.top - cardRect.height - gap);
            const left = Math.min(
                Math.max(viewportPadding, rect.left),
                Math.max(viewportPadding, window.innerWidth - cardRect.width - viewportPadding),
            );

            spotlight.hidden = false;
            spotlight.style.top = `${Math.max(4, rect.top - padding)}px`;
            spotlight.style.left = `${Math.max(4, rect.left - padding)}px`;
            spotlight.style.width = `${rect.width + (padding * 2)}px`;
            spotlight.style.height = `${rect.height + (padding * 2)}px`;
            card.style.top = `${top}px`;
            card.style.left = `${left}px`;
            card.style.transform = 'none';
        };

        const renderStep = () => {
            const step = steps[currentStep];

            if (!step) {
                closeTour();
                return;
            }

            title.textContent = step.title;
            description.textContent = step.description;
            progress.textContent = `${currentStep + 1} / ${steps.length}`;
            previousButton.hidden = currentStep === 0;
            nextButton.textContent = currentStep === steps.length - 1 ? 'Done' : 'Next';

            if (step.target) {
                step.target.scrollIntoView({ block: 'nearest', inline: 'nearest' });
            }

            window.requestAnimationFrame(() => positionCard(step.target));
        };

        const openTour = () => {
            lastFocusedElement = document.activeElement;
            steps = collectSteps();
            currentStep = 0;

            if (steps.length === 0) {
                steps = [{
                    title: 'Welcome to your page tour',
                    description: 'Use this guide to learn the main controls on each page. The tour will automatically highlight available controls as you move through it.',
                    target: null,
                }];
            }

            tour.hidden = false;
            card.hidden = false;
            previousBodyOverflow = document.body.style.overflow;
            document.body.style.overflow = 'hidden';
            renderStep();
            window.setTimeout(() => nextButton.focus(), 0);
        };

        function closeTour() {
            tour.hidden = true;
            card.hidden = true;
            spotlight.hidden = true;
            document.body.style.overflow = previousBodyOverflow;

            if (lastFocusedElement && typeof lastFocusedElement.focus === 'function') {
                lastFocusedElement.focus();
            }
        }

        launcher.addEventListener('click', openTour);
        closeButton.addEventListener('click', closeTour);
        skipButton.addEventListener('click', closeTour);
        backdrop.addEventListener('click', closeTour);

        previousButton.addEventListener('click', () => {
            if (currentStep > 0) {
                currentStep -= 1;
                renderStep();
            }
        });

        nextButton.addEventListener('click', () => {
            if (currentStep < steps.length - 1) {
                currentStep += 1;
                renderStep();
            } else {
                closeTour();
            }
        });

        document.addEventListener('keydown', (event) => {
            if (tour.hidden) {
                return;
            }

            if (event.key === 'Escape') {
                closeTour();
            }
        });

        window.addEventListener('resize', () => {
            if (!tour.hidden && steps[currentStep]?.target) {
                positionCard(steps[currentStep].target);
            }
        });
    })();
</script>
