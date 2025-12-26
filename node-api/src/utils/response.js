/**
 * Utility untuk format response API
 */

exports.success = (res, data, message = 'Success', code = 200) => {
    return res.status(code).json({
        success: true,
        message,
        data
    });
};

exports.error = (res, message = 'Internal Server Error', code = 500, error = null) => {
    const response = {
        success: false,
        message
    };

    if (error) {
        response.error = error;
    }

    return res.status(code).json(response);
};

exports.notFound = (res, message = 'Resource not found') => {
    return res.status(404).json({
        success: false,
        message
    });
};

exports.validationError = (res, message = 'Validation Error', errors = null) => {
    const response = {
        success: false,
        message
    };

    if (errors) {
        response.errors = errors;
    }

    return res.status(400).json(response);
};
