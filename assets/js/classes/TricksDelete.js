export default class TricksDelete 
{
    constructor () {
        var deleteTricksButtons = document.querySelectorAll('.delete-tricks');

        deleteTricksButtons.forEach(function (button) {
            button.addEventListener('click', function () {
                var tricksId = button.getAttribute('data-tricks-id');
                var deleteTricksModalButton = document.getElementById('modalYesButton');
                var deleteTricksModal = new bootstrap.Modal(document.getElementById('deleteTricksModal'));
                var hrefAttributeValue = deleteTricksModalButton.getAttribute('href');

                deleteTricksModalButton.setAttribute('href', hrefAttributeValue.replace('0', tricksId));
                
                deleteTricksModal.show();
            });
        });
    }
}