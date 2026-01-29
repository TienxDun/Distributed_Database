/**
 * API wrapper functions for HTTP requests
 */

import { API_BASE } from '../config.js';

/**
 * Performs a GET request
 * @param {string} endpoint - API endpoint (e.g., '/khoa', '/monhoc?id=CNTT')
 * @returns {Promise<any>} Response data
 */
export async function apiGet(endpoint) {
    // Add cache-busting timestamp to prevent browser caching
    const separator = endpoint.includes('?') ? '&' : '?';
    const cacheBuster = `${separator}_t=${Date.now()}`;

    const response = await fetch(`${API_BASE}${endpoint}${cacheBuster}`, {
        cache: 'no-store'  // Use fetch API's cache option instead of headers
    });

    if (!response.ok) {
        if (response.status === 404) {
            throw new Error('Không tìm thấy dữ liệu');
        }
        throw new Error(`HTTP ${response.status}`);
    }

    return await response.json();
}

/**
 * Performs a POST request
 * @param {string} endpoint - API endpoint
 * @param {Object} data - Request body data
 * @returns {Promise<any>} Response data
 */
export async function apiPost(endpoint, data = {}) {
    const response = await fetch(`${API_BASE}${endpoint}`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(data)
    });

    if (!response.ok) {
        const error = await response.json();
        throw new Error(error.error || 'Có lỗi xảy ra');
    }

    return await response.json();
}

/**
 * Performs a PUT request
 * @param {string} endpoint - API endpoint with query parameters
 * @param {Object} data - Request body data
 * @returns {Promise<any>} Response data
 */
export async function apiPut(endpoint, data) {
    const response = await fetch(`${API_BASE}${endpoint}`, {
        method: 'PUT',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(data)
    });

    if (!response.ok) {
        const error = await response.json();
        throw new Error(error.error || 'Có lỗi xảy ra');
    }

    return await response.json();
}

/**
 * Performs a DELETE request
 * @param {string} endpoint - API endpoint with query parameters
 * @returns {Promise<any>} Response data
 */
export async function apiDelete(endpoint) {
    const response = await fetch(`${API_BASE}${endpoint}`, {
        method: 'DELETE'
    });

    if (!response.ok) {
        const error = await response.json();
        throw new Error(error.error || 'Có lỗi xảy ra');
    }

    return await response.json();
}

/**
 * Build query string from parameters object
 * @param {Object} params - Key-value pairs for query parameters
 * @returns {string} Query string (e.g., '?key1=value1&key2=value2')
 */
export function buildQueryString(params) {
    const searchParams = new URLSearchParams();

    for (const [key, value] of Object.entries(params)) {
        if (value !== null && value !== undefined && value !== '') {
            searchParams.append(key, value);
        }
    }

    const queryString = searchParams.toString();
    return queryString ? `?${queryString}` : '';
}

// Cache for dropdown options
const optionsCache = {};

/**
 * Fetch options for select dropdown
 * @param {string} endpoint - API endpoint (e.g., '/khoa', '/monhoc')
 * @param {string} valueField - Field name for option value
 * @param {Array<string>} labelFields - Field names for option label
 * @returns {Promise<Array>} Array of {value, label} objects
 */
export async function fetchOptionsForField(endpoint, valueField, labelFields) {
    // Check cache first
    const cacheKey = `${endpoint}_${valueField}_${labelFields.join('_')}`;
    if (optionsCache[cacheKey]) {
        console.log(`[fetchOptions] Using cached options for ${endpoint}`);
        return optionsCache[cacheKey];
    }

    try {
        console.log(`[fetchOptions] Fetching options from ${endpoint}`);
        const data = await apiGet(endpoint);

        if (!Array.isArray(data) || data.length === 0) {
            return [];
        }

        // Format data into {value, label} format
        const options = data.map(item => {
            // Helper to get value case-insensitively
            const getVal = (obj, key) => {
                if (obj[key] !== undefined) return obj[key];
                const lowerKey = key.toLowerCase();
                const foundKey = Object.keys(obj).find(k => k.toLowerCase() === lowerKey);
                return foundKey ? obj[foundKey] : undefined;
            };

            const value = getVal(item, valueField);
            // Build label from multiple fields (e.g., "CNTT - Công nghệ thông tin")
            const label = labelFields.map(field => getVal(item, field)).filter(val => val !== undefined && val !== null).join(' - ');
            return { value, label };
        });

        // Cache the result
        optionsCache[cacheKey] = options;
        console.log(`[fetchOptions] Cached ${options.length} options for ${endpoint}`);

        return options;
    } catch (error) {
        console.error(`[fetchOptions] Error fetching options from ${endpoint}:`, error);
        throw error;
    }
}

/**
 * Clear options cache (useful when data changes)
 * @param {string} endpoint - Optional: clear specific endpoint, or all if not provided
 */
export function clearOptionsCache(endpoint = null) {
    if (endpoint) {
        // Clear specific endpoint cache
        Object.keys(optionsCache).forEach(key => {
            if (key.startsWith(endpoint)) {
                delete optionsCache[key];
            }
        });
        console.log(`[fetchOptions] Cleared cache for ${endpoint}`);
    } else {
        // Clear all cache
        Object.keys(optionsCache).forEach(key => delete optionsCache[key]);
        console.log(`[fetchOptions] Cleared all cache`);
    }
}
