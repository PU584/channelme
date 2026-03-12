document.getElementById('doctorImages').addEventListener('change', function(event) {
    const container = document.getElementById('selectedImageContainer');
    const files = event.target.files;

    
    if (files.length + container.childElementCount > 1) {
        alert('You can only upload one image at once.');
        event.target.value = '';
        return;
    }

    Array.from(files).forEach(file => {
        const reader = new FileReader();
        reader.onload = function(e) {
            const imgDiv = document.createElement('div');
            imgDiv.className = 'image-preview';

            const img = document.createElement('img');
            img.src = e.target.result;

            const removeBtn = document.createElement('button');
            removeBtn.className = 'remove-image-btn';
            removeBtn.innerHTML = '×';
            removeBtn.onclick = function() {
                container.removeChild(imgDiv);
            };

            imgDiv.appendChild(img);
            imgDiv.appendChild(removeBtn);
            container.appendChild(imgDiv);
        };
        reader.readAsDataURL(file);
    });
});
