
const swalDark = Swal.mixin({
    background:        '#1a1a2e',
    color:             '#e0e0e0',
    confirmButtonColor:'#7c3aed',
    cancelButtonColor: '#374151',
    customClass: {
        popup:          'swal-dark-popup',
        title:          'swal-dark-title',
        confirmButton:  'swal-dark-confirm',
        cancelButton:   'swal-dark-cancel'
    }
});

function redirectFunc(redirectID) {
    const pages = {
        1: "../view/homePage.php",
        2: "../view/loginPage.php",
        3: "../view/registPage.php",
        4: "../view/adminPage.php"
    };
    if (pages[redirectID]) window.location.href = pages[redirectID];
}


function isValidEmail(email) {
    return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email.trim());
}

function isValidUsername(uname) {
    return /^[a-zA-Z0-9_]{3,50}$/.test(uname.trim());
}

function isValidPassword(pw) {
    if (!pw) return false;
    const special = /[!@#$%^&*()_+\-=[\]{};':"\\|,.<>\/?`~]/;
    return special.test(pw);
}

function loginFunc() {
    var loguname    = document.getElementById("logun").value.trim();
    var logpassword = document.getElementById("logpass").value;

    if (!loguname || !logpassword) {
        swalDark.fire({ icon: "warning", title: "Missing Fields", text: "Enter both username and password." });
        return;
    }

    $.ajax({
        url:  "../controllers/userController.php",
        type: "POST",
        data: { luname: loguname, lpass: logpassword },
        success: function (returnedData) {
            if (returnedData == "true") {
                redirectFunc(1);
            } else {
                swalDark.fire({ icon: "error", title: "Login Failed", text: "Wrong username or password." });
            }
        },
        error: function (xhr) {
            swalDark.fire({ icon: "error", title: "Error " + xhr.status, text: xhr.responseText });
        }
    });
}

function addFunc() {
    var firstName = document.getElementById("fName").value.trim();
    var lastName  = document.getElementById("lName").value.trim();
    var userName  = document.getElementById("uName").value.trim();
    var email     = document.getElementById("email").value.trim();
    var password  = document.getElementById("password").value;

    var errors = [];
    if (!firstName) {
        errors.push("First name is required.");
    }
    if (!lastName) {
        errors.push("Last name is required.");
    }
    if (!isValidUsername(userName)) {
        errors.push("Username must be 3–50 characters (letters, numbers, underscores only).");
    }
    if (!isValidEmail(email)) {
        errors.push("Please enter a valid email address.");
    }
    if (password.length < 8) {
        errors.push("Password must be at least 8 characters.");
    } else {
        if (!isValidPassword(password)) {
            errors.push("Password must include at least one special character (e.g. !@#$%).");
        }
    }

    if (errors.length) {
        swalDark.fire({ icon: "error", title: "Invalid Input", html: errors.map(function(e){ return '• ' + e; }).join("<br>") });
        return;
    }

    $.ajax({
        url:  "../controllers/userController.php",
        type: "POST",
        data: { fName: firstName, lName: lastName, uName: userName, email, password },
        success: function (returnedData) {
            if (returnedData == "true") {
                swalDark.fire({ icon: "success", title: "Registered!", text: "Your account has been created. You can now log in." })
                    .then(() => redirectFunc(2));
            } else {
                swalDark.fire({ icon: "error", title: "Failed", text: "Registration failed: [" + returnedData + "]" });
            }
        },
        error: function (xhr) {
            swalDark.fire({ icon: "error", title: "Error " + xhr.status, text: xhr.responseText });
        }
    });
}

function updateFunc(userID) {
    var firstName = document.getElementById("txtfName").value.trim();
    var lastName  = document.getElementById("txtlName").value.trim();
    var userName  = document.getElementById("txtuName").value.trim();
    var email     = document.getElementById("txtemail").value.trim();
    var password  = document.getElementById("txtpassword").value;

    var errors = [];
    if (!firstName) {
        errors.push("First name is required.");
    }
    if (!lastName) {
        errors.push("Last name is required.");
    }
    if (!isValidUsername(userName)) {
        errors.push("Username must be 3–50 characters (letters, numbers, underscores only).");
    }
    if (!isValidEmail(email)) {
        errors.push("Please enter a valid email address.");
    }
    if (password.length < 8) {
        errors.push("Password must be at least 8 characters.");
    } else if (!isValidPassword(password)) {
        errors.push("Password must include at least one special character (e.g. !@#$%).");
    }

    if (errors.length) {
        swalDark.fire({ icon: "error", title: "Invalid Input", html: errors.map(e => `• ${e}`).join("<br>") });
        return;
    }

    swalDark.fire({ title: "Save changes?", icon: "question", showCancelButton: true, confirmButtonText: "Save" })
        .then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url:  "../controllers/userController.php",
                    type: "POST",
                    data: { ufName: firstName, ulName: lastName, uuName: userName, uemail: email, upassword: password, uid: userID },
                    success: function (returnedData) {
                        if (returnedData == "true") {
                            swalDark.fire("Updated!", "", "success").then(() => location.reload(true));
                        } else {
                            swalDark.fire({ icon: "error", title: "Failed", text: "Update failed: " + returnedData });
                        }
                    },
                    error: function (xhr) {
                        swalDark.fire({ icon: "error", title: "Error " + xhr.status, text: xhr.responseText });
                    }
                });
            }
        });
}

function deleteFunc(userID) {
    swalDark.fire({
        title: "Are you sure?",
        text:  "You won't be able to revert this!",
        icon:  "warning",
        showCancelButton:  true,
        confirmButtonText: "Yes, delete it!"
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url:  "../controllers/userController.php",
                type: "POST",
                data: { did: userID },
                success: function (returnedData) {
                    if (returnedData == "true") {
                        location.reload(true);
                    } else {
                        swalDark.fire({ icon: "error", title: "Failed", text: "Delete failed: " + returnedData });
                    }
                },
                error: function (xhr) {
                    swalDark.fire({ icon: "error", title: "Error " + xhr.status, text: xhr.responseText });
                }
            });
        }
    });
}

function openFunc(userID, fname, lname, uname, email, password) {
    document.getElementById("txtfName").value    = fname;
    document.getElementById("txtlName").value    = lname;
    document.getElementById("txtuName").value    = uname;
    document.getElementById("txtemail").value    = email;
    document.getElementById("txtpassword").value = password;

    document.getElementById("btnSave").setAttribute("onclick", "updateFunc(" + userID + ")");
    document.getElementById("myModal").style.display = "flex";

    document.getElementById("btnCloseModal").onclick = function () {
        document.getElementById("myModal").style.display = "none";
    };
}

/* =====================================================================
   VINYL FUNCTIONS - AI Assisted
   ===================================================================== */

function previewImage(inputEl, previewId) {
    const file    = inputEl.files[0];
    const preview = document.getElementById(previewId);
    if (!preview) return;

    if (!file) { preview.style.display = 'none'; preview.src = ''; return; }

    // Client-side type check
    const allowed = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
    if (!allowed.includes(file.type)) {
        swalDark.fire({ icon: 'warning', title: 'Invalid File', text: 'Only JPEG, PNG, WEBP, or GIF images are allowed.' });
        inputEl.value = '';
        preview.style.display = 'none';
        return;
    }
    if (file.size > 5 * 1024 * 1024) {
        swalDark.fire({ icon: 'warning', title: 'File Too Large', text: 'Maximum image size is 5 MB.' });
        inputEl.value = '';
        preview.style.display = 'none';
        return;
    }

    const reader = new FileReader();
    reader.onload = function (e) {
        preview.src           = e.target.result;
        preview.style.display = 'block';
    };
    reader.readAsDataURL(file);
}

function validateVinylForm(title, price, stock, year) {
    const errors = [];
    if (!title.trim())       errors.push("Title is required.");
    if (isNaN(price) || price <= 0)   errors.push("Price must be a number greater than 0.");
    if (isNaN(stock) || stock < 0 || !Number.isInteger(Number(stock)))
                              errors.push("Stock must be a whole number ≥ 0.");
    const currentYear = new Date().getFullYear();
    if (year !== '' && (isNaN(year) || year < 1900 || year > currentYear))
                              errors.push(`Release year must be between 1900 and ${currentYear}.`);
    return errors;
}

function addVinylFunc() {
    const title  = document.getElementById("v_title").value;
    const artist = document.getElementById("v_artist").value;
    const genre  = document.getElementById("v_genre").value;
    const price  = parseFloat(document.getElementById("v_price").value);
    const stock  = document.getElementById("v_stock").value;
    const year   = document.getElementById("v_year").value;
    const desc   = document.getElementById("v_desc").value;

    const errors = validateVinylForm(title, price, stock, year);
    if (errors.length) {
        swalDark.fire({ icon: "error", title: "Invalid Input", html: errors.map(e => `• ${e}`).join("<br>") });
        return;
    }

    const formData = new FormData();
    formData.append("v_title",  title.trim());
    formData.append("v_artist", artist);
    formData.append("v_genre",  genre);
    formData.append("v_price",  price);
    formData.append("v_stock",  parseInt(stock));
    formData.append("v_year",   year);
    formData.append("v_desc",   desc.trim());

    const coverFile = document.getElementById("v_cover_image");
    const vinylFile = document.getElementById("v_vinyl_image");
    if (coverFile && coverFile.files[0]) formData.append("v_cover_image", coverFile.files[0]);
    if (vinylFile && vinylFile.files[0]) formData.append("v_vinyl_image", vinylFile.files[0]);

    $.ajax({
        url:         "../controllers/vinylController.php",
        type:        "POST",
        data:        formData,
        processData: false,
        contentType: false,
        success: function (returnedData) {
            if (returnedData == "true") {
                swalDark.fire({ icon: "success", title: "Vinyl Added!", text: "The record has been saved." })
                    .then(() => location.reload(true));
            } else {
                swalDark.fire({ icon: "error", title: "Failed", text: returnedData });
            }
        },
        error: function (xhr) {
            swalDark.fire({ icon: "error", title: "Error " + xhr.status, text: xhr.responseText });
        }
    });
}

function openVinylFunc(id, title, artistId, genreId, price, stock, year, desc, coverSrc, vinylSrc) {
    document.getElementById("v_id").value      = id;
    document.getElementById("v_title").value   = title;
    document.getElementById("v_artist").value  = artistId;
    document.getElementById("v_genre").value   = genreId;
    document.getElementById("v_price").value   = price;
    document.getElementById("v_stock").value   = stock;
    document.getElementById("v_year").value    = year  || '';
    document.getElementById("v_desc").value    = desc  || '';

    const coverPrev = document.getElementById("v_cover_preview");
    const vinylPrev = document.getElementById("v_vinyl_preview");
    if (coverPrev) { coverPrev.src = coverSrc || ''; coverPrev.style.display = coverSrc ? 'block' : 'none'; }
    if (vinylPrev) { vinylPrev.src = vinylSrc || ''; vinylPrev.style.display = vinylSrc ? 'block' : 'none'; }

    document.getElementById("vinylModal").style.display = "flex";

    document.getElementById("btnCloseVinylModal").onclick = function () {
        document.getElementById("vinylModal").style.display = "none";
    };
}

function saveVinylFunc() {
    const idEl   = document.getElementById("v_id");
    const title  = document.getElementById("v_title").value;
    const artist = document.getElementById("v_artist").value;
    const genre  = document.getElementById("v_genre").value;
    const price  = parseFloat(document.getElementById("v_price").value);
    const stock  = document.getElementById("v_stock").value;
    const year   = document.getElementById("v_year").value;
    const desc   = document.getElementById("v_desc").value;

    const errors = validateVinylForm(title, price, stock, year);
    if (errors.length) {
        swalDark.fire({ icon: "error", title: "Invalid Input", html: errors.map(e => `• ${e}`).join("<br>") });
        return;
    }

    const formData = new FormData();
    if (idEl && idEl.value) formData.append("v_id", idEl.value);
    formData.append("v_title",  title.trim());
    formData.append("v_artist", artist);
    formData.append("v_genre",  genre);
    formData.append("v_price",  price);
    formData.append("v_stock",  parseInt(stock));
    formData.append("v_year",   year);
    formData.append("v_desc",   desc.trim());

    const coverFile = document.getElementById("v_cover_image");
    const vinylFile = document.getElementById("v_vinyl_image");
    if (coverFile && coverFile.files[0]) formData.append("v_cover_image", coverFile.files[0]);
    if (vinylFile && vinylFile.files[0]) formData.append("v_vinyl_image", vinylFile.files[0]);

    $.ajax({
        url:         "../controllers/vinylController.php",
        type:        "POST",
        data:        formData,
        processData: false,
        contentType: false,
        success: function (returnedData) {
            if (returnedData == "true") {
                swalDark.fire({ icon: "success", title: "Saved!", text: "Vinyl record updated." })
                    .then(() => location.reload(true));
            } else {
                swalDark.fire({ icon: "error", title: "Failed", text: returnedData });
            }
        },
        error: function (xhr) {
            swalDark.fire({ icon: "error", title: "Error " + xhr.status, text: xhr.responseText });
        }
    });
}

function deleteVinylFunc(vinylId) {
    swalDark.fire({
        title: "Delete this record?",
        text:  "This cannot be undone.",
        icon:  "warning",
        showCancelButton:  true,
        confirmButtonText: "Yes, delete it!"
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url:  "../controllers/vinylController.php",
                type: "POST",
                data: { v_delete_id: vinylId },
                success: function (returnedData) {
                    if (returnedData == "true") {
                        location.reload(true);
                    } else {
                        swalDark.fire({ icon: "error", title: "Failed", text: "Delete failed: " + returnedData });
                    }
                },
                error: function (xhr) {
                    swalDark.fire({ icon: "error", title: "Error " + xhr.status, text: xhr.responseText });
                }
            });
        }
    });
}

// DATA TABLE

$(document).ready(function () {
    if ($('#myTable').length) $('#myTable').DataTable();
});
