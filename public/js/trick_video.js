let linkInput ,linkButton, linkErrorMessage;
function trickVideosInit() {
    linkInput = document.querySelector('.link-input');
    linkButton = document.querySelector('.link-button');
    linkErrorMessage = document.querySelector('.link-error-message');
    document.querySelector('#videos').dataset.index = 0;


    linkButton.addEventListener('click', onClickAddLink);
}

function onClickAddLink() {
    if (linkInput.value === '') {
        return;
    }

    linkErrorMessage.textContent = '';
    const link = findYoutubeCode(linkInput.value);
    const prototype = getLinkPrototype();

    addVideo(prototype, link);
}

function addVideo(prototype, link) {
    const video = createVideo(prototype, link);

    document
        .querySelector('.list-video')
        .append(video);

    linkInput.value = '';
}

function findYoutubeCode(link) {

    test = /https:\/\/youtu.be\/(?<link>[\w\-]{11})/.exec(link);

    if (test === null) {
        test = /https:\/\/www.youtube.com\/watch\?v=(?<link>[\w\-]{11})/.exec(link);
    }

    if (test === null) {
        linkErrorMessage.textContent = 'Le lien n\est pas valide';
    }
    return test.groups.link;
}

function createVideo(prototype, link) {
    html = document.createElement('html');
    html.innerHTML = `
        <div>
            ${prototype}
            <span>
                <iframe width="560" height="315" 
                src="https://www.youtube.com/embed/${link}" 
                title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; 
                clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" allowfullscreen>
                </iframe>
            </span>
            <span class='delete-video-button' >Supprimer</span>
        </div>
    `;
    const video = html.querySelector('div');
    video.querySelector('input.video-form').value = link;
    video.querySelector('.delete-video-button').addEventListener('click', function (e) {
        e.target.parentElement.remove();
    })
    return video;
}

function getLinkPrototype() {
    const linkArea = document.querySelector('#videos');
    const { prototype, index } = linkArea.dataset;
    const linkPrototype = prototype.replace(/__name__/g, index);
    linkArea.dataset.index++;
    return linkPrototype;
}