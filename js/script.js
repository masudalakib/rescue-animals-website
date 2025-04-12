function loadAnimals() {
    const animalsContainer = document.getElementById('animalsContainer');
    if (!animalsContainer) return;

    // Show loading state
    animalsContainer.innerHTML = `
        <div class="col-span-3 text-center py-8">
            <div class="animate-spin rounded-full h-12 w-12 border-t-2 border-b-2 border-green-600 mx-auto"></div>
            <p class="mt-4 text-gray-600">Loading animals...</p>
        </div>
    `;

    fetch('php/get_animals.php')
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                if (data.animals.length > 0) {
                    displayAnimals(data.animals);
                } else {
                    showNoAnimalsMessage(data.message);
                }
            } else {
                showError(data.message || 'Failed to load animals.');
            }
        })
        .catch(error => {
            console.error('Error loading animals:', error);
            showError('Failed to load animals. Please try again later.');
        });

    function displayAnimals(animals) {
        animalsContainer.innerHTML = '';
        
        animals.forEach(animal => {
            const animalCard = document.createElement('div');
            animalCard.className = 'animal-card bg-white rounded-xl shadow-md overflow-hidden hover:shadow-lg transition duration-300';
            
            animalCard.innerHTML = `
                <div class="relative">
                    <img src="${animal.image_url}" alt="${animal.name}" class="w-full h-64 object-cover">
                    <div class="absolute top-2 right-2 ${animal.status === 'adopted' ? 'bg-green-600' : 'bg-red-600'} text-white text-xs font-bold px-2 py-1 rounded-full">
                        ${animal.status === 'adopted' ? 'Adopted' : 'Needs Home'}
                    </div>
                </div>
                <div class="p-6">
                    <h3 class="text-xl font-bold text-gray-800 mb-2">${animal.name}</h3>
                    <div class="flex items-center text-gray-600 mb-2">
                        <i class="fas fa-paw mr-2"></i>
                        <span>${animal.type.charAt(0).toUpperCase() + animal.type.slice(1)}</span>
                    </div>
                    <div class="flex items-center text-gray-600 mb-4">
                        <i class="fas fa-venus-mars mr-2"></i>
                        <span>${animal.gender === 'male' ? 'Male' : 'Female'}</span>
                        <span class="mx-2">•</span>
                        <i class="fas fa-birthday-cake mr-2"></i>
                        <span>${animal.age} years</span>
                    </div>
                    <p class="text-gray-600 mb-4">${animal.description.substring(0, 100)}${animal.description.length > 100 ? '...' : ''}</p>
                    ${animal.status !== 'adopted' ? 
                        `<button onclick="openAdoptionModal('${animal.id}')" class="w-full bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded-lg transition duration-300">
                            Adopt Me
                        </button>` : 
                        '<button disabled class="w-full bg-gray-400 text-white font-bold py-2 px-4 rounded-lg cursor-not-allowed">Already Adopted</button>'}
                </div>
            `;
            
            animalsContainer.appendChild(animalCard);
        });
    }

    function showNoAnimalsMessage(message) {
        animalsContainer.innerHTML = `
            <div class="col-span-3 text-center py-8">
                <i class="fas fa-paw text-4xl text-gray-400 mb-4"></i>
                <p class="text-gray-600">${message || 'No animals available for adoption at the moment. Please check back later.'}</p>
            </div>
        `;
    }

    function showError(message) {
        animalsContainer.innerHTML = `
            <div class="col-span-3 text-center py-8">
                <i class="fas fa-exclamation-triangle text-4xl text-red-400 mb-4"></i>
                <p class="text-gray-600">${message || 'Failed to load animals. Please try again later.'}</p>
                <button onclick="loadAnimals()" class="mt-4 bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded-lg transition duration-300">
                    <i class="fas fa-sync-alt mr-2"></i> Try Again
                </button>
            </div>
        `;
    }
}