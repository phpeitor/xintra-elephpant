(function () {
    "use strict";
  
    //To choose date
    flatpickr(".flatpickr-input", {});
  
    //Custom Validation
    const buttonSubmit = document.getElementsByClassName("ti-custom-validate-btn");
    const form = document.querySelector(".ti-custom-validation");
  
    /*input values*/
    const firstNameInput        = document.querySelector('.firstName ');
    const lastNameInput         = document.querySelector('.lastName');
    const phoneInput            = document.querySelector('.phonenumber');
    const emailInput            = document.querySelector('.email-address');
    const documentoInput        = document.querySelector('.documento');
    const checkboxInput         = document.querySelector('.validationCheckbox');
  
  /*error values*/
    const firstNameError        = document.getElementsByClassName("firstNameError ")[0];
    const lastNameError         = document.getElementsByClassName("lastNameError")[0];
    const phoneError            = document.getElementsByClassName("phoneError")[0];
    const emailError            = document.getElementsByClassName("emailError")[0];
    const documentoError        = document.getElementsByClassName("documentoError")[0];
    const checkboxError         = document.getElementsByClassName("checkboxError")[0];
  
    //define and declare and empty errors object
    let error = {};

    form.addEventListener("submit", function (e) {
      e.preventDefault();
      checkEmpty();
    });
  
    // validate empty fields and set error object
    function checkEmpty() {
      //loop and remove all key and value fields in the errors object
      for (let key in error) {
        delete error[key];
      }

      firstNameError.style.display = "none";
      lastNameError.style.display = "none";
      emailError.style.display = "none";
      phoneError.style.display="none";
      documentoError.style.display="none";
      checkboxError.style.display="none";
  
      //remove all the error class "border-red-500 classes"
      firstNameInput?.classList.remove("!border-red");
      lastNameInput?.classList.remove("!border-red");
      phoneInput?.classList.remove("!border-red");
      emailInput?.classList.remove("!border-red");
      documentoInput?.classList.remove("!border-red");
      checkboxInput?.classList.remove("!border-red");
      
  
      const firstNameValue        = firstNameInput.value.trim();
      const lastNameValue         = lastNameInput.value.trim();;
      const phoneValue            = phoneInput.value.trim();
      const emailValue            = emailInput.value.trim();
      const documentoValue        = documentoInput.value.trim();
      const checkboxValue         = checkboxInput.value.trim();
  
      if (firstNameValue === "") {
        error.firstName = "First Name is required";
      }
      if (lastNameValue === "") {
        error.lastName = "Last Name is required";
      }
      
      if (phoneValue === "") {
        error.phone = "Phone Number is required";
      }
      if (emailValue === "") {
        error.email = "Email is required";
      }
      if (documentoValue === "") {
        error.documento = "Documento is required";
      }
      if (checkboxValue === "") {
        error.checkbox = "You must agree before submitting";
      }
  
      //validate the inputs firstName and lastName
      if (firstNameValue !== "") {
        if (!firstNameValue.match(/^[a-zA-Z0-9]+$/)) {
          error.firstName = "First Name must be letters only";
        }
      }
      if (lastNameValue !== "") {
        if (!lastNameValue.match(/^[a-zA-Z0-9]+$/)) {
          error.lastName = "Last Name must be letters only";
        }
      }
      if (phoneValue !== "") {
        if (!phoneValue.match(/^[0-9]+$/)) {
          error.phone = "phone number must be numbers only";
        }
      }
      if (emailValue !== "") {
        //validating an email
        if (!emailValue.match(/^[a-zA-Z0-9.]+@[a-zA-Z0-9]+\.[a-zA-Z0-9]+$/)) {
          error.email = "Email must be a valid email";
        }
      }
      if (documentoValue !== "") {
        if (!documentoValue.match(/^[0-9]+$/)) {
          error.documento = "Documento must be numbers only";
        }
      }
      
      //if we have error add the error to the error message
      if (Object.keys(error).length > 0) {
        displayError();
      } else {
        //submit the form with a delay of 2 seconds
        //change the button innerText to submitting and add no-cursor class and disabled attribute to it
        buttonSubmit.value = "Submitting...";
        buttonSubmit.setAttribute("disabled", "disabled");
  
        //set a delay of 2 seconds since we dont have an api endpoint to send the data to just mimic the process
        new Promise(function (resolve, reject) {
          setTimeout(function () {
            resolve(submitForm());
          }, 2000);
        });
      }
    }
  
    //display errors respectivey to the span html classes
    function displayError() {
        //set all errors to their respectivey and also changing hidden 
      // error containers to be a block.
      if(error.firstName) {
        firstNameInput.classList.add("!border-red");
        firstNameError.style.display = "block";
        firstNameError.innerHTML = error.firstName;
      }
      if (error.lastName) {
        lastNameInput.classList.add("!border-red");
        lastNameError.style.display = "block";
        lastNameError.innerHTML = error.lastName;
      }
      if (error.email) {
        //loop over the classes and add other classes
        emailInput.classList.add("!border-red");
        emailError.style.display = "block";
        emailError.innerHTML = error.email;
      }
      if (error.phone) {
        //loop over the classes and add other classes
        phoneInput.classList.add("!border-red");
        phoneError.style.display = "block";
        phoneError.innerHTML = error.phone;
      }
      if (error.documento) {
        //loop over the classes and add other classes
        documentoInput.classList.add("!border-red");
        documentoError.style.display = "block";
        documentoError.innerHTML = error.documento;
      }
    }
  
    //submitting the form
    function submitForm() {

      buttonSubmit.value = "Login Now";
      buttonSubmit.removeAttribute("disabled");
  
      //reset the form and clear all fields
      form.reset();
    }
  
  })();