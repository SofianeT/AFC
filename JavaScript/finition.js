modifications = [];

window.onload = function() {
    $(".inFHF").change(function() {
        modifications.push($(this).attr("id"));
    });
    
    document.getElementById("frmValiderFicheFrais").onsubmit = function() {
        if (modifications.length > 0) {
            alert("Certaines modifications n'ont pas été enregistré. Attention !");
            modifications.forEach(function(input) {
                $("#"+input).css('background-color', '#ffb896');
            });
            return false;
        } else {
            return confirm("Valider cette fiche de frais ?");
        }
    };
};