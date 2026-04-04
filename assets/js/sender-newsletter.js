(function () {
  function initNewsletter(root) {
    if (!root || root.dataset.foundationSenderNewsletterReady === "true") {
      return;
    }

    var form = root.querySelector("form.foundation-sender-newsletter");
    if (!form) {
      return;
    }

    var message = form.querySelector(".foundation-sender-newsletter__message");
    var button = form.querySelector('button[type="submit"]');
    var defaultButtonText = root.getAttribute("data-button-text") || (button ? button.textContent : "Join the list");
    var successMessage = root.getAttribute("data-success-message") || "";
    var ajaxUrl = root.getAttribute("data-ajax-url") || "/wp-admin/admin-ajax.php";
    var nonce = root.getAttribute("data-nonce") || "";
    var groupId = root.getAttribute("data-group-id") || "";

    root.dataset.foundationSenderNewsletterReady = "true";

    function setMessage(text, state) {
      if (!message) {
        return;
      }

      message.textContent = text || "";
      message.className = "foundation-sender-newsletter__message";

      if (state) {
        message.classList.add(state);
      }
    }

    function parseJsonResponse(response) {
      return response.text().then(function (text) {
        if (!text) {
          return {};
        }

        try {
          return JSON.parse(text);
        } catch (error) {
          return {};
        }
      });
    }

    form.addEventListener("submit", function (event) {
      event.preventDefault();

      if (!button) {
        return;
      }

      var firstNameField = form.querySelector('[name="first_name"]');
      var emailField = form.querySelector('[name="email"]');
      var honeypotField = form.querySelector('[name="company"]');

      var firstName = firstNameField ? firstNameField.value.trim() : "";
      var email = emailField ? emailField.value.trim() : "";
      var company = honeypotField ? honeypotField.value.trim() : "";
      var interestFields = form.querySelectorAll('input[name="interests[]"]:checked');

      setMessage("", "");

      if (!email) {
        setMessage("Please enter your email address.", "is-error");
        if (emailField) {
          emailField.focus();
        }
        return;
      }

      if (emailField && typeof emailField.checkValidity === "function" && !emailField.checkValidity()) {
        setMessage("Please enter a valid email address.", "is-error");
        emailField.focus();
        return;
      }

      button.disabled = true;
      button.textContent = "Joining...";
      form.setAttribute("aria-busy", "true");

      var data = new URLSearchParams();
      data.append("action", "foundation_sender_subscribe");
      data.append("nonce", nonce);
      data.append("first_name", firstName);
      data.append("email", email);
      data.append("company", company);
      data.append("group_id", groupId);
      Array.prototype.forEach.call(interestFields, function (field) {
        data.append("interests[]", field.value);
      });

      fetch(ajaxUrl, {
        method: "POST",
        headers: {
          "Content-Type": "application/x-www-form-urlencoded; charset=UTF-8"
        },
        body: data.toString()
      })
        .then(function (response) {
          return parseJsonResponse(response).then(function (json) {
            return { response: response, json: json };
          });
        })
        .then(function (result) {
          var response = result.response;
          var json = result.json || {};
          var payload = json.data || {};

          if (!response.ok || !json.success) {
            throw new Error(payload.message || "Something went wrong. Please try again.");
          }

          setMessage(successMessage || payload.message || "You’re in. Please check your inbox if confirmation is needed.", "is-success");
          form.reset();
          button.textContent = "Joined";
        })
        .catch(function (error) {
          setMessage(error.message || "Connection issue. Please try again.", "is-error");
          button.textContent = "Try again";
        })
        .finally(function () {
          form.removeAttribute("aria-busy");
          window.setTimeout(function () {
            button.disabled = false;
            button.textContent = defaultButtonText;
          }, 2500);
        });
    });
  }

  function initAll(scope) {
    var root = scope || document;
    var widgets = root.querySelectorAll("[data-foundation-sender-newsletter]");
    Array.prototype.forEach.call(widgets, initNewsletter);
  }

  if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", function () {
      initAll(document);
    });
  } else {
    initAll(document);
  }

  window.addEventListener("load", function () {
    initAll(document);
  });

  if (window.elementorFrontend && window.elementorFrontend.hooks) {
    window.elementorFrontend.hooks.addAction("frontend/element_ready/foundation-sender-newsletter.default", function ($scope) {
      initAll($scope[0] || $scope);
    });
  } else {
    window.addEventListener("elementor/frontend/init", function () {
      if (window.elementorFrontend && window.elementorFrontend.hooks) {
        window.elementorFrontend.hooks.addAction("frontend/element_ready/foundation-sender-newsletter.default", function ($scope) {
          initAll($scope[0] || $scope);
        });
      }
    });
  }
})();
