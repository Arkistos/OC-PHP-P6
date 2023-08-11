let groupAutocompleteInput, groupAutocompleteList, groupAddButton;

function trickGroupsInit() {
    groupAutocompleteInput = document.querySelector('.group-autocomplete-input');
    groupAutocompleteList = document.querySelector('.group-autocomplete-list');
    groupAddButton = document.querySelector('.group-add-button');
    document.querySelector('#groups').dataset.index = 0;

    groupAutocompleteInput.addEventListener('input', autocompleteInputChange);

    groupAddButton.addEventListener("click", onClickAddGroup);


    /*** Gestion des catégories pour l'édition ****/
    const existingGroups = document.querySelectorAll('div[id^="trick_form_group_"]');

    existingGroups.forEach(element => {
        const id = element.querySelector('input.group-id').value
        const name = element.querySelector('input.group-name').value
        const prototype = getGroupPrototype();
        addGroupPills(prototype, { id, name });
    })
    document.querySelector('#trick_form_group').remove();
}

function onClickAddGroup() {
    if (groupAutocompleteInput.value == '') {
        return '';
    }

    const prototype = getGroupPrototype();
    const group = getGroupValue();

    addGroupPills(prototype, group);
}

function addGroupPills(prototype, group) {
    const groupPill = createGroupPill(prototype, group);

    document
        .querySelector('span.list-groups')
        .append(groupPill);

    groupAutocompleteInput.value = '';
    groupAutocompleteList.innerHTML = '';
}

function getGroupPrototype() {
    const groupsArea = document.querySelector('#groups');
    const { prototype, index } = groupsArea.dataset;
    const groupPrototype = prototype.replace(/__name__/g, index);
    groupsArea.dataset.index++;
    return groupPrototype;
}

function getGroupValue() {
    const inputValue = groupAutocompleteInput.value;
    let group = groupsData.find((item) => inputValue === item.name);
    if (!group) {
        return { id: '', name: inputValue }
    }
    return group
}

function createGroupPill(prototype, group) {
    const html = `
        <div>
            ${prototype}
            <span>${group.name}</span>
            <span class='delete-group-button'>X</span>
        </div>
    `;

    groupPill = document.createElement('html');
    groupPill.innerHTML = html;
    groupPill = groupPill.querySelector('div');
    groupPill.querySelector('input.group-id').value = group.id;
    groupPill.querySelector('input.group-name').value = group.name;
    groupPill.querySelector('.delete-group-button').addEventListener('click', function (e) {
        e.target.parentElement.remove();
    });
    return groupPill;
}

function autocompleteInputChange() {
    if (groupAutocompleteInput.value === '') {
        groupAutocompleteList.innerHTML = '';
        return;
    }

    const regex = new RegExp(`${groupAutocompleteInput.value}`, 'i');
    groupAutocompleteList.innerHTML = groupsData
        .filter(item => regex.test(item.name))
        .map(item => `<div class="group-autocomplete-option" data-id="${item.id}" data-name="${item.name}">
                ${item.name.replace(regex, (char) => `<strong>${char}</strong>`)}
            </div>`)
        .join('');

    groupAutocompleteList
        .querySelectorAll('.group-autocomplete-option')
        .forEach((option) => option.addEventListener('click', function (e) {
            const prototype = getGroupPrototype();
            const { id, name } = e.target.dataset;
            addGroupPills(prototype, { id, name });
        }))

}