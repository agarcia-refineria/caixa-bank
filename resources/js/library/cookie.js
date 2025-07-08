window.cookie = {
    set: function (name, value, options = {}) {
        let cookieStr = `${encodeURIComponent(name)}=${encodeURIComponent(value)}`;

        if (options.expires) {
            let date = new Date();
            date.setTime(date.getTime() + options.expires * 24 * 60 * 60 * 1000);
            cookieStr += `; expires=${date.toUTCString()}`;
        }

        if (options.path) {
            cookieStr += `; path=${options.path}`;
        } else {
            cookieStr += `; path=/`; // default to root
        }

        document.cookie = cookieStr;
    },

    get: function (name) {
        const cookies = document.cookie.split(';');
        for (let cookie of cookies) {
            let [key, value] = cookie.trim().split('=');
            if (key === decodeURIComponent(name)) {
                return decodeURIComponent(value);
            }
        }
        return null;
    },

    delete: function (name) {
        document.cookie = `${encodeURIComponent(name)}=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;`;
    }
};
