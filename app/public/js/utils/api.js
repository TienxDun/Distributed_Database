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
export async function apiPost(endpoint, data) {
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
