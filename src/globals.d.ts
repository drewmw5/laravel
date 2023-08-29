export {};

import Echo from 'laravel-echo';
    
declare global {
    function route(routeName?: string, parameters?: any[] | any, absolute? = true): Function[string]

    interface Window {
        Echo: Echo;
    }
}
