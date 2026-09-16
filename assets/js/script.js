function togglePassword(id){

    let input=document.getElementById(id);

    input.type=input.type==="password"?"text":"password";

}

// Password Strength

const password=document.getElementById("password");

if(password){

password.addEventListener("keyup",function(){

let value=password.value;

let strength=0;

if(value.length>=8) strength++;

if(/[A-Z]/.test(value)) strength++;
if(/[0-9]/.test(value)) strength++;
if(/[^A-Za-z0-9]/.test(value)) strength++;

let bar=document.getElementById("strengthBar");
let text=document.getElementById("strengthText");

switch(strength){

case 1:
bar.style.width="25%";
bar.className="progress-bar bg-danger";
text.innerHTML="Weak";
break;

case 2:
bar.style.width="50%";
bar.className="progress-bar bg-warning";
text.innerHTML="Medium";
break;

case 3:
bar.style.width="75%";
bar.className="progress-bar bg-info";
text.innerHTML="Good";
break;

case 4:
bar.style.width="100%";
bar.className="progress-bar bg-success";
text.innerHTML="Strong";
break;

default:
bar.style.width="0";
text.innerHTML="";
}

});

}

// Confirm Password

const confirmPassword=document.getElementById("confirmPassword");

if(confirmPassword){

confirmPassword.addEventListener("keyup",function(){

let msg=document.getElementById("matchMessage");

if(confirmPassword.value===password.value){

msg.innerHTML="✔ Passwords Match";

msg.style.color="green";

}else{

msg.innerHTML="✖ Passwords Do Not Match";

msg.style.color="red";

}

});

}

document.getElementById("registerForm")?.addEventListener("submit",function(){

document.getElementById("registerBtn").innerHTML='<span class="spinner-border spinner-border-sm"></span> Creating Account...';

});