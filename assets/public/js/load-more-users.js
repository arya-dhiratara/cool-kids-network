document.addEventListener("DOMContentLoaded", function () {
    const loadMoreButton = document.getElementById("load-more-users");
    const userList = document.getElementById("coolkids-user-list");

    if (!loadMoreButton || !userList) return;

    loadMoreButton.addEventListener("click", function () {
        let offset = parseInt(loadMoreButton.dataset.offset, 10);

        // Hide the button instead of removing it
        loadMoreButton.style.display = "none";

        // Create and insert the loading animation
        const loader = document.createElement("div");
        loader.classList.add("loading-animation");
        loader.innerHTML = `
            <svg version="1.1" id="L4" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 52 90" xml:space="preserve">
                <circle fill="#333" cx="6" cy="50" r="6">
                    <animate attributeName="opacity" dur="1s" values="0;1;0" repeatCount="indefinite" begin="0.1"></animate>    
                </circle>
                <circle fill="#333" cx="26" cy="50" r="6">
                    <animate attributeName="opacity" dur="1s" values="0;1;0" repeatCount="indefinite" begin="0.2"></animate>       
                </circle>
                <circle fill="#333" cx="46" cy="50" r="6">
                    <animate attributeName="opacity" dur="1s" values="0;1;0" repeatCount="indefinite" begin="0.3"></animate>     
                </circle>
            </svg>
        `;
        loadMoreButton.insertAdjacentElement("beforebegin", loader);

        fetch(coolKidsAjax.ajax_url, {
            method: "POST",
            headers: {
                "Content-Type": "application/x-www-form-urlencoded",
            },
            body: new URLSearchParams({
                action: "load_more_users",
                nonce: coolKidsAjax.nonce,
                offset: offset,
            }),
        })
        .then(response => response.json())
        .then(data => {
            loader.remove(); // Remove loader

            if (data.success) {
                // Create a temporary wrapper
                const tempDiv = document.createElement("div");
                tempDiv.innerHTML = data.data.html;

                // Apply fade-in effect to new users
                Array.from(tempDiv.children).forEach(child => {
                    child.classList.add("fade-in");
                    userList.appendChild(child);
                });

                loadMoreButton.dataset.offset = offset + 12;

                // Auto-scroll to new users smoothly
                userList.lastElementChild.scrollIntoView({ behavior: "smooth" });

                if (data.data.has_more) {
                    loadMoreButton.style.display = "block"; // Show button again
                } else {
                    loadMoreButton.remove(); // No more users, remove button
                }
            } else {
                console.error("Failed to load more users:", data.data.message);
                loadMoreButton.style.display = "block"; // Show button again if failed
            }
        })
        .catch(error => {
            console.error("AJAX Error:", error);
            loader.remove();
            loadMoreButton.style.display = "block"; // Show button again if error occurs
        });
    });
});
