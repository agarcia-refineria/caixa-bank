document.addEventListener('DOMContentLoaded', () => {
    console.log('Institutions script loaded');
    window.setCountryInstitution = setCountryInstitution;
    window.addInstitution = addInstitution;
    window.removeInstitution = removeInstitution;
    window.resetInstitutionsHTML = resetInstitutionsHTML;

    let institutionsInfoDiv = document.getElementById('institutions-info');
    let institutionsInput = document.getElementById('institutions');
    const institutionsContainer = document.querySelector('.js-institutions');

    /** COUNTRY INSTITUTIONS FUNCTIONS */
    function setCountryInstitution(country) {
        const institutions = document.querySelectorAll('.js-institutions-list h3');
        const currentInstitutions = institutionsInput.value ? institutionsInput.value.split(',') : [];

        institutions.forEach((institution) => {
            if (institution.getAttribute('data-country') !== country || currentInstitutions.includes(institution.getAttribute('data-institution-id'))) {
                institution.style.display = 'none';
            } else {
                institution.style.display = null;
            }
        });
    }

    function addInstitution(institutionId, institutionName, institutionLogo) {
        const currentInstitutions = institutionsInput.value ? institutionsInput.value.split(',') : [];

        if (!currentInstitutions.includes(institutionId)) {
            currentInstitutions.push(institutionId);
            institutionsInput.value = currentInstitutions.join(',');
        }

        setInformation(institutionId, institutionName, institutionLogo);

        resetInstitutionsHTML();
    }

    function removeInstitution(institutionId) {
        let currentInstitutions = institutionsInput.value ? institutionsInput.value.split(',') : [];

        currentInstitutions = currentInstitutions.filter(id => id !== institutionId);
        institutionsInput.value = currentInstitutions.join(',');

        const institutionInfo = document.getElementById(`institution-${institutionId}`);
        if (institutionInfo) {
            institutionInfo.remove();
        }

        resetInstitutionsHTML();
    }

    function resetInstitutionsHTML() {
        institutionsContainer.innerHTML = '';

        const currentInstitutions = institutionsInput.value ? institutionsInput.value.split(',') : [];

        if (currentInstitutions.length === 0) {
            let text = institutionsInfoDiv.getAttribute('data-empty');
            institutionsContainer.innerHTML = '<p class="text-sm text-secondary">'+text+'</p>';
            return;
        }

        currentInstitutions.forEach((institutionId) => {
            const institutionInfo = document.getElementById(`institution-${institutionId}`);

            const institutionElement = document.createElement('div');
            if (institutionInfo.getAttribute('data-linked') === 'true') {
                institutionElement.className = 'flex items-center justify-between p-2 bg-main2 text-white border-2 border-third rounded-md';
            } else {
                institutionElement.className = 'flex items-center justify-between p-2 bg-main2 text-white border-2 border-main3 rounded-md';
            }

            institutionElement.innerHTML = `
                    <img width="32" height="32" src="${institutionInfo.getAttribute('data-logo')}" alt="${institutionInfo.getAttribute('data-name')}" class="inline-block mr-2">
                    <span>${institutionInfo.getAttribute('data-name')}</span>
                    <button type="button" class="text-error hover:opacity-50" onclick="removeInstitution('${institutionId}')">
                        &times;
                    </button>
                `;
            institutionsContainer.appendChild(institutionElement);
        });
    }

    function setInformation(institutionId, institutionName, institutionLogo) {
        const institutionInfo = document.createElement('div');
        institutionInfo.id = `institution-${institutionId}`;
        institutionInfo.setAttribute('data-name', institutionName);
        institutionInfo.setAttribute('data-logo', institutionLogo);
        institutionInfo.setAttribute('data-linked', 'false');
        institutionInfo.style.display = 'none'; // Hide this element, it's just for reference
        institutionsInfoDiv.appendChild(institutionInfo);
    }

    /** SEARCH FUNCTIONS */
    let searchCountryInput = document.getElementById('search-country');

    searchCountryInput.addEventListener('input', () => {
        const country = searchCountryInput.value.toLowerCase();
        const countries = document.querySelectorAll('.js-country-list h3');

        countries.forEach((countryElement) => {
            if (countryElement.getAttribute('data-searchable').toLowerCase().includes(country)) {
                countryElement.classList.remove('hidden');
            } else {
                countryElement.classList.add('hidden');
            }
        });
    });

    let searchInstitutionInput = document.getElementById('search-institutions');
    searchInstitutionInput.addEventListener('input', () => {
        const institution = searchInstitutionInput.value.toLowerCase();
        const institutions = document.querySelectorAll('.js-institutions-list h3');

        institutions.forEach((institutionElement) => {
            if (institutionElement.getAttribute('data-searchable').toLowerCase().includes(institution)) {
                institutionElement.classList.remove('hidden');
            } else {
                institutionElement.classList.add('hidden');
            }
        });
    });
});
