document.addEventListener('DOMContentLoaded', function() {
    // Mobile menu toggle
    const mobileMenuButton = document.querySelector('.mobile-menu-button');
    const mobileMenu = document.querySelector('.mobile-menu');
    
    mobileMenuButton.addEventListener('click', function() {
        mobileMenu.classList.toggle('active');
    });

    // Smooth scrolling for navigation links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function(e) {
            e.preventDefault();
            
            const targetId = this.getAttribute('href');
            if (targetId === '#') return;
            
            const targetElement = document.querySelector(targetId);
            if (targetElement) {
                targetElement.scrollIntoView({
                    behavior: 'smooth'
                });
                
                // Close mobile menu if open
                mobileMenu.classList.remove('active');
            }
        });
    });

    // Form submissions
    const reportForm = document.getElementById('reportForm');
    const adoptionForm = document.getElementById('adoptionForm');
    const contactForm = document.getElementById('contactForm');
    
    if (reportForm) {
        reportForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            
            fetch('php/submit_animal.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    document.getElementById('reportSuccess').classList.remove('hidden');
                    reportForm.reset();
                    setTimeout(() => {
                        document.getElementById('reportSuccess').classList.add('hidden');
                    }, 5000);
                } else {
                    alert('Error submitting report: ' + (data.message || 'Please try again later.'));
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred. Please try again later.');
            });
        });
    }
    
    if (contactForm) {
        contactForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            
            fetch('php/contact.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    document.getElementById('contactSuccess').classList.remove('hidden');
                    contactForm.reset();
                    setTimeout(() => {
                        document.getElementById('contactSuccess').classList.add('hidden');
                    }, 5000);
                } else {
                    alert('Error sending message: ' + (data.message || 'Please try again later.'));
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred. Please try again later.');
            });
        });
    }

    // Load animals for adoption
    loadAnimals();

    // Adoption modal functionality
    const adoptionModal = document.getElementById('adoptionModal');
    const closeModal = document.getElementById('closeModal');
    
    if (closeModal) {
        closeModal.addEventListener('click', function() {
            adoptionModal.classList.add('hidden');
        });
    }
    
    if (adoptionForm) {
        adoptionForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            
            fetch('php/submit_adoption.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    document.getElementById('adoptionSuccess').classList.remove('hidden');
                    adoptionForm.reset();
                    setTimeout(() => {
                        document.getElementById('adoptionSuccess').classList.add('hidden');
                        adoptionModal.classList.add('hidden');
                    }, 3000);
                } else {
                    alert('Error submitting adoption request: ' + (data.message || 'Please try again later.'));
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred. Please try again later.');
            });
        });
    }
});

function loadAnimals() {
    const animalsContainer = document.getElementById('animalsContainer');
    if (!animalsContainer) return;
    
    fetch('php/get_animals.php')
        .then(response => response.json())
        .then(data => {
            if (data.success && data.animals.length > 0) {
                animalsContainer.innerHTML = '';
                
                data.animals.forEach(animal => {
                    const animalCard = document.createElement('div');
                    animalCard.className = 'animal-card bg-white rounded-xl shadow-md overflow-hidden hover:shadow-lg transition duration-300';
                    
                    animalCard.innerHTML = `
                        <div class="relative">
                            <img src="${animal.image_url}" alt="${animal.name}" class="w-full h-64 object-cover">
                            ${animal.status === 'adopted' ? 
                                '<div class="absolute top-2 right-2 bg-green-600 text-white text-xs font-bold px-2 py-1 rounded-full">Adopted</div>' : 
                                '<div class="absolute top-2 right-2 bg-red-600 text-white text-xs font-bold px-2 py-1 rounded-full">Needs Home</div>'}
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
                            <p class="text-gray-600 mb-4">${animal.description.substring(0, 100)}...</p>
                            ${animal.status !== 'adopted' ? 
                                `<button onclick="openAdoptionModal('${animal.id}')" class="w-full bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded-lg transition duration-300">
                                    Adopt Me
                                </button>` : 
                                '<button disabled class="w-full bg-gray-400 text-white font-bold py-2 px-4 rounded-lg cursor-not-allowed">Already Adopted</button>'}
                        </div>
                    `;
                    
                    animalsContainer.appendChild(animalCard);
                });
            } else {
                animalsContainer.innerHTML = `
                    <div class="col-span-3 text-center py-8">
                        <i class="fas fa-paw text-4xl text-gray-400 mb-4"></i>
                        <p class="text-gray-600">No animals available for adoption at the moment. Please check back later.</p>
                    </div>
                `;
            }
        })
        .catch(error => {
            console.error('Error loading animals:', error);
            animalsContainer.innerHTML = `
                <div class="col-span-3 text-center py-8">
                    <i class="fas fa-exclamation-triangle text-4xl text-red-400 mb-4"></i>
                    <p class="text-gray-600">Failed to load animals. Please try again later.</p>
                </div>
            `;
        });
}

function openAdoptionModal(animalId) {
    const adoptionModal = document.getElementById('adoptionModal');
    const animalIdField = document.getElementById('animalId');
    
    if (adoptionModal && animalIdField) {
        animalIdField.value = animalId;
        adoptionModal.classList.remove('hidden');
        document.getElementById('adoptionSuccess').classList.add('hidden');
    }
}