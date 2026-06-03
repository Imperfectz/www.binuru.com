
const swalDark = Swal.mixin({
    background:        '#1a1a1a',
    color:             '#f0ede8',
    confirmButtonColor:'#f5e642',
    cancelButtonColor: '#374151',
    customClass: {
        popup:         'swal-dark-popup',
        title:         'swal-dark-title',
        confirmButton: 'swal-dark-confirm',
        cancelButton:  'swal-dark-cancel'
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
    const special = /[!@#$%^&*()\-_=+[\]{};':"\\|,.<>/?`~]/;
    return special.test(pw);
}

function setFieldError(inputEl, msg) {
    inputEl.classList.add('is-error');
    inputEl.classList.remove('is-valid');
    const hint = inputEl.parentElement && inputEl.parentElement.querySelector('.field-hint');
    if (hint) { hint.textContent = msg; hint.style.display = 'block'; }
}

function clearFieldError(inputEl) {
    inputEl.classList.remove('is-error');
    const hint = inputEl.parentElement && inputEl.parentElement.querySelector('.field-hint');
    if (hint) hint.style.display = 'none';
}

function markValid(inputEl) {
    inputEl.classList.remove('is-error');
    inputEl.classList.add('is-valid');
    const hint = inputEl.parentElement && inputEl.parentElement.querySelector('.field-hint');
    if (hint) hint.style.display = 'none';
}

function validateField(inputEl, rules) {
    const val = inputEl.value;
    for (const { test, msg } of rules) {
        if (!test(val)) { setFieldError(inputEl, msg); return false; }
    }
    markValid(inputEl);
    return true;
}

$(document).ready(function () {

    const logEmail = document.getElementById('logemail');
    const logPass  = document.getElementById('logpass');
    if (logEmail) {
        logEmail.addEventListener('blur', function () {
            if (!this.value.trim()) {
                setFieldError(this, 'Email is required.');
            } else if (!isValidEmail(this.value)) {
                setFieldError(this, 'Enter a valid email address.');
            } else {
                clearFieldError(this);
            }
        });
        logEmail.addEventListener('input', function () {
            if (this.classList.contains('is-error') && isValidEmail(this.value)) clearFieldError(this);
        });
    }
    if (logPass) {
        logPass.addEventListener('blur', function () {
            if (!this.value) setFieldError(this, 'Password is required.');
            else clearFieldError(this);
        });
        logPass.addEventListener('input', function () {
            if (this.classList.contains('is-error') && this.value) clearFieldError(this);
        });
    }

    const fields = {
        fName:    [{ test: v => v.trim().length > 0,     msg: 'First name is required.' }],
        lName:    [{ test: v => v.trim().length > 0,     msg: 'Last name is required.' }],
        uName:    [{ test: v => isValidUsername(v),       msg: 'Username: 3–50 chars, letters/numbers/_ only.' }],
        email:    [{ test: v => isValidEmail(v),          msg: 'Enter a valid email address.' }],
        password: [
            { test: v => v.length >= 8,                  msg: 'Password must be at least 8 characters.' },
            { test: v => isValidPassword(v),             msg: 'Include at least one special character (!@#$%…).' }
        ]
    };

    Object.entries(fields).forEach(([id, rules]) => {
        const el = document.getElementById(id);
        if (!el) return;
        el.addEventListener('blur', function () {
            for (const { test, msg } of rules) {
                if (!test(this.value)) { setFieldError(this, msg); return; }
            }
            clearFieldError(this);
        });
        el.addEventListener('input', function () {
            if (!this.classList.contains('is-error')) return;
            const allPass = rules.every(({ test }) => test(this.value));
            if (allPass) clearFieldError(this);
        });
    });

    // DataTable
    if ($('#myTable').length) $('#myTable').DataTable();
});

function loginFunc() {
    const emailEl = document.getElementById("logemail");
    const passEl  = document.getElementById("logpass");
    const logemail    = emailEl.value.trim();
    const logpassword = passEl.value;

    let ok = true;
    if (!logemail) { setFieldError(emailEl, 'Email is required.'); ok = false; }
    else if (!isValidEmail(logemail)) { setFieldError(emailEl, 'Enter a valid email address.'); ok = false; }
    else clearFieldError(emailEl);

    if (!logpassword) { setFieldError(passEl, 'Password is required.'); ok = false; }
    else clearFieldError(passEl);

    if (!ok) return;

    $.ajax({
        url:  "../controllers/userController.php",
        type: "POST",
        data: { lemail: logemail, lpass: logpassword },
        success: function (returnedData) {
            if (returnedData == "true") {
                redirectFunc(1);
            } else {
                setFieldError(emailEl, ' ');
                setFieldError(passEl, 'Wrong email or password.');
                swalDark.fire({ icon: "error", title: "Login Failed", text: "Wrong email or password." });
            }
        },
        error: function (xhr) {
            swalDark.fire({ icon: "error", title: "Error " + xhr.status, text: xhr.responseText });
        }
    });
}

function addFunc() {
    const fNameEl = document.getElementById("fName");
    const lNameEl = document.getElementById("lName");
    const uNameEl = document.getElementById("uName");
    const emailEl = document.getElementById("email");
    const passEl  = document.getElementById("password");

    const firstName = fNameEl.value.trim();
    const lastName  = lNameEl.value.trim();
    const userName  = uNameEl.value.trim();
    const email     = emailEl.value.trim();
    const password  = passEl.value;

    let ok = true;

    if (!firstName) { setFieldError(fNameEl, 'First name is required.'); ok = false; } else clearFieldError(fNameEl);
    if (!lastName)  { setFieldError(lNameEl, 'Last name is required.');  ok = false; } else clearFieldError(lNameEl);

    if (!isValidUsername(userName)) {
        setFieldError(uNameEl, 'Username: 3–50 chars, letters/numbers/_ only.');
        ok = false;
    } else clearFieldError(uNameEl);

    if (!isValidEmail(email)) {
        setFieldError(emailEl, 'Enter a valid email address.');
        ok = false;
    } else clearFieldError(emailEl);

    if (password.length < 8) {
        setFieldError(passEl, 'Password must be at least 8 characters.');
        ok = false;
    } else if (!isValidPassword(password)) {
        setFieldError(passEl, 'Include at least one special character (!@#$%…).');
        ok = false;
    } else clearFieldError(passEl);

    if (!ok) return;

    $.ajax({
        url:  "../controllers/userController.php",
        type: "POST",
        data: { fName: firstName, lName: lastName, uName: userName, email, password },
        success: function (returnedData) {
            if (returnedData.trim() == "true") {
                swalDark.fire({ icon: "success", title: "Registered!", text: "Your account has been created. You can now log in." })
                    .then(() => redirectFunc(2));
            } else {
                // Show server-side message (e.g. email already in use)
                swalDark.fire({ icon: "error", title: "Registration Failed", text: returnedData });
                if (returnedData.includes('Email')) setFieldError(emailEl, returnedData);
                if (returnedData.includes('Username')) setFieldError(uNameEl, returnedData);
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
    if (!firstName) errors.push("First name is required.");
    if (!lastName)  errors.push("Last name is required.");
    if (!isValidUsername(userName)) errors.push("Username must be 3–50 characters (letters, numbers, underscores only).");
    if (!isValidEmail(email)) errors.push("Please enter a valid email address.");

    if (password.length > 0) {
        if (password.length < 8) {
            errors.push("New password must be at least 8 characters.");
        } else if (!isValidPassword(password)) {
            errors.push("New password must include at least one special character (e.g. !@#$%).");
        }
    }

    if (errors.length) {
        swalDark.fire({ icon: "error", title: "Invalid Input", html: errors.map(e => `• ${e}`).join("<br>") });
        return;
    }

    swalDark.fire({ title: "Save changes?", icon: "question", showCancelButton: true, confirmButtonText: "Save" })
        .then((result) => {
            if (result.isConfirmed) {
                var postData = { ufName: firstName, ulName: lastName, uuName: userName, uemail: email, uid: userID };
                if (password.length > 0) postData.upassword = password;

                $.ajax({
                    url:  "../controllers/userController.php",
                    type: "POST",
                    data: postData,
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


