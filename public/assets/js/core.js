function openSidebar() {
    let sidebar = document.getElementById('sidebar');
    sidebar.classList.add('fixed', 'top-0', 'right-0', 'left-0', 'bottom-0', 'z-10');
    sidebar.classList.remove('hidden');
}

function closeSidebar() {
    let sidebar = document.getElementById('sidebar');
    sidebar.classList.remove('fixed', 'top-0', 'right-0', 'left-0', 'bottom-0', 'z-10');
    sidebar.classList.add('hidden');
}

// let openModal = document.querySelectorAll('.modal-open')
// for (let i = 0; i < openModal.length; i++) {
//     openModal[i].addEventListener('click', function (event) {
//         event.preventDefault()
//         toggleModal(event)
//     })
// }
// const overlay = document.querySelector('.modal-overlay')
// overlay.addEventListener('click', toggleModal)
// overlay.addEventListener('click', toggleUserModal)
//
// let closeModal = document.querySelectorAll('.modal-close')
// for (let i = 0; i < closeModal.length; i++) {
//     closeModal[i].addEventListener('click', toggleModal)
//     closeModal[i].addEventListener('click', toggleUserModal)
// }

function toggleModal(event) {
    let button = event.target;
    let categoryId = button.dataset['categoryid']
    let categoryName = ' ' + button.dataset['categoryname']
    let categoryTitle = document.getElementById('categoryDeleteTitle')
    let categoryLink = document.getElementById('categoryDeleteLink')
    if (categoryTitle) {
        categoryTitle.innerText = 'Supprimer la catégorie' + categoryName
    }
    if (categoryLink) {
        categoryLink.href = 'http://localhost/admin/categories/' + categoryId + '/delete'
    }
    const body = document.querySelector('body')
    const modal = document.querySelector('.modal')
    modal.classList.toggle('opacity-0')
    modal.classList.toggle('pointer-events-none')
    body.classList.toggle('modal-active')
}

function toggleUserModal() {
    const body = document.querySelector('body')
    const modal = document.querySelector('.modal')
    modal.classList.toggle('opacity-0')
    modal.classList.toggle('pointer-events-none')
    body.classList.toggle('modal-active')
}

let postSelect = document.querySelector('.js_post_select');
let addCommentLink = document.querySelector('.js_comment_add');

postSelect.addEventListener('change', updateAddCommentLink);

function updateAddCommentLink(event) {
    let selectedPostId = event.target.value;
    addCommentLink.setAttribute('href', '/admin/posts/' + selectedPostId + '/comment')
}