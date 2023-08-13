let modal, closeButton, cancelButton, deleteLinks;
function modalInit(){
    modal = document.querySelector("#modal");
    closeButton = document.querySelector("#modal-close");
    cancelButton = document.querySelector("#modal-cancel");
    deleteLinks = document.querySelectorAll('.delete-button');

    closeButton.addEventListener("click", closeModal);

    cancelButton.addEventListener("click", closeModal);

    deleteLinks.forEach((link)=>{
        link.addEventListener('click', function(e){
        displayModal(e.target.id, e.target.getAttribute('slug'));
        })
    });
}

function displayModal(trickName, trickSlug){
            
    let modalTrickName = document.querySelector("#trickName");
    let deleteTrickButton = document.querySelector("#modal-delete-trick");
    modalTrickName.innerHTML = trickName;
    deleteTrickButton.href = 'trick/remove/'+trickSlug;

    modal.style.display = 'block';
}

function closeModal(){
    modal.style.display = 'none';
}