const moment = require("moment-timezone");

/**
 * Get current WIB timestamp for MySQL
 */
exports.getWIBTimestamp = () => {
    return moment().tz("Asia/Jakarta").format("YYYY-MM-DD HH:mm:ss");
};

/**
 * Convert UTC to WIB
 */
exports.convertToWIB = (date) => {
    return moment(date).tz("Asia/Jakarta").format("YYYY-MM-DD HH:mm:ss");
};

/**
 * Format date to WIB
 */
exports.formatWIB = (date, format = "DD MMM YYYY HH:mm") => {
    return moment(date).tz("Asia/Jakarta").format(format);
};
