export default class InputFileList {

    fileInput;
    fileList;

    constructor (inputName, listName) {
        this.fileInput = document.getElementById(inputName);
        this.fileList = document.getElementById(listName);

        this.fileInput.addEventListener('change', this.updateFileList.bind(this));
    }

    updateFileList() {
        this.fileList.innerHTML = '';

        for (const file of this.fileInput.files) {
            const listItem = document.createElement('li');
            listItem.classList.add('fileItem');

            const fileName = document.createElement('span');
            fileName.classList.add('fileName');
            fileName.textContent = file.name;

            const removeButton = document.createElement('button');
            removeButton.classList.add('removeButton');
            removeButton.addEventListener('click', () => {
                this.removeFile(file);
            });

            const removeButtonIcon = document.createElement('i');
            removeButtonIcon.classList.add('fa-solid', 'fa-trash');

            removeButton.appendChild(removeButtonIcon);
            listItem.appendChild(fileName);
            listItem.appendChild(removeButton);

            this.fileList.appendChild(listItem);
        }
    }

    removeFile(file) {
        const filesArray = Array.from(this.fileInput.files);
        const index = filesArray.indexOf(file);
        
        if (index !== -1) {
            filesArray.splice(index, 1); // Supprimer le fichier du tableau
            const newFileList = new DataTransfer();
            
            // Ajouter les fichiers restants à la nouvelle FileList
            for (const remainingFile of filesArray) {
                newFileList.items.add(remainingFile);
            }
            
            this.fileInput.files = newFileList.files;
            this.updateFileList();
        }
    }
}