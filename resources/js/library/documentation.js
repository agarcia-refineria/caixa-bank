import Shepherd from 'shepherd.js';
import 'shepherd.js/dist/css/shepherd.css';

document.addEventListener('DOMContentLoaded', () => {
    const routeName = document.querySelector("head").getAttribute('dir');
    const nextTranslation = document.querySelector("head").getAttribute('next-translation');
    const closeTranslation = document.querySelector("head").getAttribute('close-translation');


    // Set cookies for navigation and profile true if they do not exist
    if (!window.cookie.get('documentation_tour_navigation')) {
        window.cookie.set('documentation_tour_navigation', 'true', { expires: 10 / (24 * 60) }); // Set cookie to expire in 10 minutes
    }
    if (!window.cookie.get('documentation_tour_navigation_profile')) {
        window.cookie.set('documentation_tour_navigation_profile', 'true', { expires: 10 / (24 * 60) }); // Set cookie to expire in 10 minutes
    }

    // Trigger tour start
    function startTour() {
        let loadNavigation = window.cookie.get('documentation_tour_navigation') === 'true';
        let loadNavigationProfile = window.cookie.get('documentation_tour_navigation_profile') === 'true';

        let tour = new Shepherd.Tour({
            useModalOverlay: true,
            defaultStepOptions: {
                classes: 'c-shepherd',
                scrollTo: true,
                scrollToHandler: function (element) {
                    // Scroll to the element with a smooth behavior + 200 offset top
                    const topOffset = 200;
                    const elementPosition = element.getBoundingClientRect().top + window.pageYOffset;
                    window.scrollTo({
                        top: elementPosition - topOffset,
                        behavior: 'smooth'
                    });
                }
            }
        });

        // Define navigation steps default
        if (loadNavigation) {
            addStep(tour,'navigation-panel-control');

            addStep(tour,'navigation-history');

            addStep(tour,'navigation-forecast');

            addStep(tour,'navigation-clock');

            addStep(tour,'navigation-configuration');

            if (routeName === "dashboard.index" || routeName === "dashboard.show") {
                addStep(tour, 'navigation-month-selector');
            }

            addStep(tour,'navigation-lang-selector');

            addStep(tour,'navigation-user-dropdown');

            window.cookie.set('documentation_tour_navigation', 'false', { expires: 10 / (24 * 60) }); // Set cookie to expire in 10 minutes
        }

        // Navigation steps for profile
        if (loadNavigationProfile && (routeName === "profile.edit" || routeName === "profile.configuration.edit" || routeName === "profile.accounts.edit" || routeName === "profile.categories")) {
            addStep(tour, 'profile-navigation-profile');
            addStep(tour, 'profile-navigation-accounts');
            addStep(tour, 'profile-navigation-categories');
            addStep(tour, 'profile-navigation-logs');
            addStep(tour, 'profile-navigation-bank');

            window.cookie.set('documentation_tour_navigation_profile', 'false', { expires: 10 / (24 * 60) }); // Set cookie to expire in 10 minutes
        }

        console.log("Load documentation tour for route:", routeName);
        loadIndexTour(tour);
        loadHistoryTour(tour);
        loadForecastTour(tour);
        loadClockTour(tour);
        loadRequestsTour(tour);
        loadProfileTour(tour);
        loadProfileAccountsTour(tour);
        loadProfileCategoriesTour(tour);
        loadProfileConfigurationTour(tour);

        // Check if the tour has steps
        if (tour.steps.length === 0) {
            // If no steps are defined add a default step
            addStep('default-step');
            return;
        }

        tour.start();
    }

    function addStep(tour, id, position = 'top', text = null) {
        if (!text) {
            text = document.querySelector(`#${id}`) ? document.querySelector(`#${id}`).getAttribute('shepherd-text') : '';
        }

        if (document.getElementById(id) === null) {
            console.warn(`Element with ID '${id}' not found. Step will not be added.`);
            return;
        }

        tour.addStep({
            id: id,
            text: text,
            attachTo: {
                element: "#"+id,
                on: position
            },
            buttons: [
                {
                    text: closeTranslation,
                    action: tour.cancel
                },
                {
                    text: nextTranslation,
                    action: tour.next
                }
            ]
        });
    }

    window.routeName = routeName;
    window.startTour = startTour;
    window.addStep = addStep;
})

// Index page
import '/resources/js/library/documentation/index.js';

// History page
import '/resources/js/library/documentation/history.js';

// Forecast page
import '/resources/js/library/documentation/forecast.js';

// Clock page
import '/resources/js/library/documentation/clock.js';

// Configuration page
import '/resources/js/library/documentation/requests.js';

// Profile page
import '/resources/js/library/documentation/profile/profile.js';

// Profile Bank page
import '/resources/js/library/documentation/profile/configuration.js';

// Profile Accounts page
import '/resources/js/library/documentation/profile/accounts.js';

// Profile Categories page
import '/resources/js/library/documentation/profile/categories.js';
