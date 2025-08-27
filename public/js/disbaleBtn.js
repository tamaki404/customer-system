//disable button for some time after submit
document.getElementById('submitForm').addEventListener('submit', function(event) {
    let button = document.getElementById('submitBtn');
    button.disabled = true;
    button.innerText = "Processing...";
});
