function fetchJobTemplates() {
    const routeUrl = window.ROUTES.WEB.JOBTEMPLATE.INDEX;
    fetch(routeUrl)
        .then(response => response.json())
        .then(data => {
            console.log('Job templates fetched successfully:', data);
            // Process the data as needed
        })
        .catch(error => {
            console.error('Error fetching job templates:', error);
        });
}
//=====================================================================
document.addEventListener('DOMContentLoaded', function() {
  
});