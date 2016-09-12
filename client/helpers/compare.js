module.exports = function compare(first, second, options) {
    if (first === second) {
        return options.fn(this);
    } else {
        return options.inverse(this);
    }
};
