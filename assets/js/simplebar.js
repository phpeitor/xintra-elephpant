(function () {
    "use strict";
    
    var myElement = document.getElementById('sidebar-scroll');
    if (myElement) {
        new SimpleBar(myElement, { autoHide: true });
    }
    
})();