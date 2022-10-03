window.onload = function() {
    showPopup();
  };



function done() { 
    document.getElementById("popup").style.display = "none";
    var password = document.getElementById("pass").value;
    if(password=='4'){
    prompt('oi')
    }
    else{
    prompt("no")
    }
    //DO STUFF WITH PASSWORD HERE    
};
  
function showPopup() {
    document.getElementById("popup").style.display = "block";
    var input = document.getElementById("pass");
    input.focus();
    // Execute a function when the user presses a key on the keyboard
    input.addEventListener("keypress", function(event) {
        console.log("oui");
    // If the user presses the "Enter" key on the keyboard
    if (event.key === "Enter") {
        // Cancel the default action, if needed
        event.preventDefault();
        // Trigger the button element with a click
        document.getElementById("buttonOK").click();
    }
    });
}