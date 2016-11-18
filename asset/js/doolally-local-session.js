/*stored keys mailcheck */

localStorageUtil =
{
    getLocal:function(key)
    {
        if (typeof (Storage) == "undefined")
        {
            return false;
        }

        try
        {
            var record = JSON.parse(localStorage.getItem(key));
            if (!record)
            {
                return null;
            }
            if(new Date().getTime() < record.timestamp && JSON.parse(record.value))
            {
                return JSON.parse(record.value);
            }
            else
            {
                return null;
            }
        }
        catch (e)
        {
            return null;
        }
    },
    setLocal:function(key, jsonData, expirationMS)
    {
        if (typeof (Storage) == "undefined")
        {
            return false;
        }
        /*var expirationMS = expirationMin * 60 * 1000;*/
        if (typeof (expirationMS) == "undefined")
        {
            expirationMS = 7 * 24 * 60 * 60 * 1000;
        }
        var record =
        {
            value: JSON.stringify(jsonData),
            timestamp: new Date().getTime() + expirationMS
        };
        localStorage.setItem(key, JSON.stringify(record));
        return jsonData;
    },
    delLocal:function(key)
    {
        localStorage.removeItem(key);
    },
    getSession:function(key)
    {
        return sessionStorage.getItem(key);
    },
    setSession:function(key, value)
    {
        sessionStorage.setItem(key, value);
    },
    delSession:function(key)
    {
        sessionStorage.removeItem(key);
    },
    clearAllSession:function ()
    {
        sessionStorage.clear();
    }
};

