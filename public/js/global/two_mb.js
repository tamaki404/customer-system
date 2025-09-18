

                    document.addEventListener('DOMContentLoaded', function(){
                        var MAX = 2 * 1024 * 1024; // 2MB
                        var allowedTypes = ['image/png', 'image/jpeg', 'image/jpg', 'image/webp'];

                        var profilePicInput = document.querySelector('.modal-option-groups input[type="file"][name="image"]');
                        var errorMessage = document.querySelector('.modal-option-groups .error-message');

                        if (profilePicInput) {
                            profilePicInput.addEventListener('change', function(){
                                errorMessage.textContent = ''; // clear previous error

                                var file = profilePicInput.files[0];
                                if (!file) return; // nothing selected

                                if (file.size > MAX) {
                                    errorMessage.textContent = 'File must be 2MB or less.';
                                    profilePicInput.value = '';
                                    return;
                                }

                                if (!allowedTypes.includes(file.type)) {
                                    errorMessage.textContent = 'Only PNG, JPEG, JPG, or WEBP images are allowed.';
                                    profilePicInput.value = '';
                                    return;
                                }
                            });
                        }
                    });
