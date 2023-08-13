let loadButton;
let page, pages;
function loadMoreInit(currentPage, numberPages){
    page = currentPage;
    pages = numberPages;
    
    let loadButton = document.querySelector('.button-load');

    loadButton.addEventListener('click', onLoadButton);
}

function onLoadButton(e){
    console.log(page,pages);
    page ++;
        if (page>=pages){
            e.target.remove();
        }
    getTricksPaginated(page);
}



function getTricksPaginated(page){
    fetch('/json-tricks/'+page, {
        method: 'GET',
        headers: {
            'Accept': 'application/json',
        },
    })
    .then(function(response){
        return response.json();
    })
    .then((response)=>{
        tricks = response;
        createTricks(tricks);
        document.querySelector('.cta-up').style.display = 'block';
        
    });
}

function createTricks(tricks){
    for (const key in tricks){
        let newCard = document.createElement('div');
        if(tricks.hasOwnProperty(key)){
            newCard.className= 'card';
            newListPills = document.createElement('div');
            newListPills.className = 'list-pills';
            newCard.append(newListPills);
            for(const groupKey in tricks[key]['group']){
                newListPills.innerHTML +='<li class="pills">'+ tricks[key]['group'][groupKey].name +'</li>';
            }
            newImg = document.createElement('div');
            newImgtag = document.createElement('img');
            newImgtag.className = 'card-picture';
            let id = tricks[key]['id'];
            let pics = tricks[key]['pictures'];
            
            if(pics.length == 0){
                newImgtag.src = "assets/uploads/tricks_pictures/default-picture.png";
            } else {
                newImgtag.src = "assets/uploads/tricks_pictures/"+ id +"-"+ pics[0].id +".webp"
            } 
            newImg.append(newImgtag);
            newCard.append(newImg);

            let cardName = document.createElement('h3');
            cardName.className ='card-name';
            cardName.innerHTML = '<a href="trick/'+tricks[key]['slug']+'">'+tricks[key]['name']+'</a>'
            newCard.append(cardName);

            let cardManagement = document.createElement('div');
            cardManagement.className = 'card-management';
            cardManagement.innerHTML = '<a href="trick/edit/' + tricks[key]['slug'] + '"><img src="assets/images/icon-edit.png" alt=""></a><img class="delete-button" id='+ tricks[key]['name'] +' slug='+ tricks[key]['slug'] +' src="assets/images/icon-trash.png" alt="">'

            newCard.append(cardManagement);
        }
        let listTricks = document.querySelector('#list-tricks');
        listTricks.append(newCard);
    }
    modalInit();
}