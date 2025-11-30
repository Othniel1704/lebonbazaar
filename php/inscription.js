function valider() {
    let result = true; // Initialiser à true
    const errorMessages = document.querySelectorAll(".erreur");
    for (let i = 0; i < errorMessages.length; i++) {
        errorMessages[i].remove();
    }

    // Check email
    const ctrlemail = document.getElementsByName("email")[0];
    const valmail = ctrlemail.value ;
    const regex3 = /^[^\s][^\s@A-Z]+@[^\s@A-Z]+\.[^\s@A-Z]+$/;
    const isokmail = regex3.test(valmail);
    if (!isokmail) {
        const div = document.createElement("div");
        div.className = "erreur";
        ctrlemail.parentElement.appendChild(div);
        div.innerHTML = "Veuillez entrer une adresse email valide";
        ctrlemail.style.borderColor = "red";
        result = false;
    }

    // Check password
    const ctrlmdp = document.getElementsByName("mdp")[0];
    const lemdp = ctrlmdp.value;
    const passwordRegex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{10,}$/;
    const isokmdp = passwordRegex.test(lemdp);
    if (!isokmdp) {
        const div = document.createElement("div");
        div.className = "erreur";
        ctrlmdp.parentElement.appendChild(div);
        div.innerHTML = "Le mot de passe doit contenir au moins 10 caractères, une majuscule, une minuscule, un chiffre et un caractère spécial";
        ctrlmdp.style.borderColor = "red";
        result = false;
    }

    // Check confirmation password
    const ctrlconfmdp = document.getElementsByName("confmdp")[0];   
    const leconfmdp = ctrlconfmdp.value;
    if (lemdp != leconfmdp) {
        const div = document.createElement("div");
        div.className = "erreur";
        ctrlconfmdp.parentElement.appendChild(div);
        div.innerHTML = "Les mots de passe ne sont pas identiques";
        ctrlconfmdp.style.borderColor = "red";
        result = false;
    }

    // Check prenom              
    const ctrlprenom = document.getElementsByName('prenom')[0];
    const leprenom = ctrlprenom.value;
    const regex2 = new RegExp("^[A-Za-zéèà^]+$");
    const isokprenom = regex2.test(leprenom);
    if (!isokprenom) {
        const div = document.createElement("div");
        div.className = "erreur";
        ctrlprenom.parentElement.appendChild(div);
        div.innerHTML = "Le prénom doit contenir uniquement des lettres minuscules ET MAGISCULES";
        ctrlprenom.style.borderColor = "red";
        result = false;
    }

    // Check phone number
    const ctrlphone = document.getElementsByName("tel")[0];
    const valphone = ctrlphone.value ;
    const regex4 = /^\d{10}$/;
    const isokphone = regex4.test(valphone);
    if (!isokphone) {
        const div = document.createElement("div");
        div.className = "erreur";
        ctrlphone.parentElement.appendChild(div);
        div.innerHTML = "Veuillez entrer un numéro de téléphone valide";
        ctrlphone.style.borderColor = "red";
        result = false;
    }

    return result;
}