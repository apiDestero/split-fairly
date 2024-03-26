Application = (function() {

    var Application = function (parameters) {

        this.errors = parameters.errors ?? [];
        this.users = parameters.users ?? [];
        this.pay = parseFloat(parameters.pay) ?? 100;

        if (!Array.isArray(this.errors)) this.errors = [];
        if (!Array.isArray(this.users)) this.users = [];

        this.init();
    }

    Application.prototype =
    {
        init: function() {
        
            this.userTable = document.getElementById('userTable');					
            this.userForm = document.getElementById("userForm");
            this.messagesTarget = document.getElementById("messages");

            if (this.userForm)
                this.userForm.addEventListener("submit", this.onSubmit.bind(this));
        },

        onSubmit: function(event) {

            event.preventDefault();

            this.messagesTarget.innerHTML = '';

            const form = event.target;

            this.toggleButtons(form, true);

            var xhr = new XMLHttpRequest();

            xhr.withCredentials = false;

            xhr.addEventListener('readystatechange', () => {
                if (xhr.readyState === 4)
                {
                    try {
                        var data = JSON.parse(xhr.responseText);
                    } catch (e) {
                        var data = {errors: [e]}
                    }

                    if (data.users && Array.isArray(data.users)) this.users = data.users;
                    if (this.pay) this.pay = parseFloat(data.pay);

                    this.renderUsers(this.users, this.pay);

                    if (data.errors && Array.isArray(data.errors) && data.errors.length > 0)
                        this.showErrors(data.errors);

                    this.toggleButtons(form, false);
                }
            });

            var data = new FormData(form);
            var method = form.getAttribute('method') ?? 'post';
            var url = form.getAttribute('action') ?? '/';

            if (event.submitter.name == 'reset')
                data.append('reset', true)
        
            xhr.open(method, url);
            xhr.send(data);
        },

        renderUsers: function(users, pay) {

            if (this.userTable)
            {
                var target = this.userForm.getElementsByTagName('tbody');
                if (target && target.length > 0)
                {
                    target[0].innerHTML = '';

                    if (Array.isArray(users) && users.length > 0)
                    {
                        users.forEach((user) => {

                            newRow = target[0].insertRow();

                            newRow.insertCell()
                                .appendChild(document.createTextNode(user.name));
                            newRow.insertCell()
                                .appendChild(document.createTextNode(user.email));
                            newRow.insertCell()
                                .appendChild(document.createTextNode(pay));
                        });
                    }
                }
            }
        },

        toggleButtons: function(form, disable = false) {

            var submitButtons = form.querySelectorAll('button[type="submit"], input[type="submit"]');
            submitButtons.forEach(function(button) {
                if (disable) button.disabled = true;
                else button.disabled = false;
            });
        },

        showErrors: function(errors) {
            this.messagesTarget.innerHTML = errors.join('</br>');
        }
    }

    return Application;
})();