// =====================================================
// Response Helper Utility
// =====================================================

/**
 * Success response format
 */
const success = (res, data, message = 'Success', statusCode = 200) => {
  return res.status(statusCode).json({
    success: true,
    message,
    data
  });
};

/**
 * Error response format
 */
const error = (res, message = 'Internal server error', statusCode = 500, error = null) => {
  const response = {
    success: false,
    message
  };

  if (error) {
    response.error = error;
  }

  return res.status(statusCode).json(response);
};

/**
 * Not found response
 */
const notFound = (res, message = 'Resource not found') => {
  return error(res, message, 404);
};

/**
 * Validation error response
 */
const validationError = (res, message = 'Validation failed', errors = null) => {
  const response = {
    success: false,
    message
  };

  if (errors) {
    response.errors = errors;
  }

  return res.status(400).json(response);
};

module.exports = {
  success,
  error,
  notFound,
  validationError
};