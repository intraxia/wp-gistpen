const data = {};
let store = Symbol('store');
let reducer = Symbol('reducer');
let initialized = false;

export function initialize(callback, initialState = {}) {
    if (initialized) {
        throw new Error('Store was reinitialized');
    }

    data[store] = initialState;
    data[reducer] = callback;

    update();
}

export function fetch() {
    return data[store];
}

export function dispatch(event) {
    data[store] = data[reducer](event, data[store]);

    update();
}

const callbacks = [];

export function subscribe(callback) {
    callbacks.push(callback);
}

function update() {
    callbacks.forEach((cb) => cb(fetch()));
}