/**
 * Configuration and constants for the application
 */

// Dynamically determine API base URL
// On Render, both UI and API share the same origin
// On Local Docker, UI (port 8081) needs to talk to API (port 8080)
export const API_BASE = (window.location.hostname === 'localhost' || window.location.hostname === '127.0.0.1')
    ? (window.location.port === '8081' ? 'http://localhost:8080' : '')
    : window.location.origin;

export const MODULES = {
    KHOA: 'khoa',
    MONHOC: 'monhoc',
    SINHVIEN: 'sinhvien',
    CTDAOTAO: 'ctdaotao',
    DANGKY: 'dangky',
    GLOBAL: 'global'
};

export const FIELDS_CONFIG = {
    khoa: [
        {
            name: 'MaKhoa',
            label: 'Mã Khoa',
            type: 'text',
            required: true,
            maxlength: 10,
            readonly: 'edit',
            placeholder: 'Ví dụ: CNTT, NN, LUAT'
        },
        {
            name: 'TenKhoa',
            label: 'Tên Khoa',
            type: 'text',
            required: true
        }
    ],
    monhoc: [
        {
            name: 'MaMH',
            label: 'Mã Môn Học',
            type: 'text',
            required: true,
            maxlength: 10,
            readonly: 'edit',
            placeholder: 'Ví dụ: MH001, MH002'
        },
        {
            name: 'TenMH',
            label: 'Tên Môn Học',
            type: 'text',
            required: true
        }
    ],
    sinhvien: [
        {
            name: 'MaSV',
            label: 'Mã Sinh Viên',
            type: 'text',
            required: true,
            maxlength: 20,
            readonly: 'edit',
            placeholder: 'Ví dụ: 25DH000001, 24DH000002'
        },
        {
            name: 'HoTen',
            label: 'Họ Tên',
            type: 'text',
            required: true
        },
        {
            name: 'MaKhoa',
            label: 'Khoa',
            type: 'select',
            required: true,
            optionsFrom: '/khoa',
            optionValue: 'MaKhoa',
            optionLabel: ['MaKhoa', 'TenKhoa'],  // Hiển thị: "CNTT - Công nghệ thông tin"
            placeholder: 'Chọn khoa'
        },
        {
            name: 'KhoaHoc',
            label: 'Khóa Học',
            type: 'number',
            required: true,
            min: 2015,
            max: 2030,
            placeholder: 'Năm nhập học (2015-2030)'
        }
    ],
    ctdaotao: [
        {
            name: 'MaKhoa',
            label: 'Khoa',
            type: 'select',
            required: true,
            optionsFrom: '/khoa',
            optionValue: 'MaKhoa',
            optionLabel: ['MaKhoa', 'TenKhoa'],
            placeholder: 'Chọn khoa'
        },
        {
            name: 'KhoaHoc',
            label: 'Khóa Học',
            type: 'number',
            required: true,
            min: 2015,
            max: 2030
        },
        {
            name: 'MaMH',
            label: 'Môn Học',
            type: 'select',
            required: true,
            optionsFrom: '/monhoc',
            optionValue: 'MaMH',
            optionLabel: ['MaMH', 'TenMH'],
            placeholder: 'Chọn môn học'
        }
    ],
    dangky: {
        create: [
            {
                name: 'MaSV',
                label: 'Sinh Viên',
                type: 'select',
                required: false,  // Không bắt buộc vì có MaSV_input thay thế
                optionsFrom: '/sinhvien',
                optionValue: 'MaSV',
                optionLabel: ['MaSV', 'HoTen'],
                placeholder: 'Chọn sinh viên từ danh sách'
            },
            {
                name: 'MaSV_input',
                label: 'Hoặc nhập mã sinh viên',
                type: 'text',
                required: false,
                maxlength: 20,
                placeholder: 'Nhập trực tiếp mã sinh viên (ví dụ: 25DH000001)'
            },
            {
                name: 'MaMon',
                label: 'Môn Học',
                type: 'select',
                required: true,
                optionsFrom: '/monhoc',
                optionValue: 'MaMH',
                optionLabel: ['MaMH', 'TenMH'],
                placeholder: 'Chọn môn học'
            },
            {
                name: 'DiemThi',
                label: 'Điểm Thi',
                type: 'number',
                required: false,
                min: 0,
                max: 10,
                step: 0.01,
                placeholder: 'Để trống nếu chưa có điểm (0-10)'
            }
        ],
        edit: [
            {
                name: 'MaSV',
                label: 'Mã Sinh Viên',
                type: 'text',
                readonly: true,
                lockMessage: '🔒 Mã sinh viên không thể chỉnh sửa'
            },
            {
                name: 'MaMon',
                label: 'Mã Môn Học',
                type: 'text',
                readonly: true,
                lockMessage: '🔒 Mã môn học không thể chỉnh sửa'
            },
            {
                name: 'DiemThi',
                label: 'Điểm Thi',
                type: 'number',
                required: true,
                min: 0,
                max: 10,
                step: 0.01,
                placeholder: 'Nhập điểm từ 0 đến 10'
            }
        ]
    }
};

export const MODAL_TITLES = {
    khoa: {
        create: '➕ Thêm Khoa Mới',
        edit: '✏️ Sửa Thông Tin Khoa'
    },
    monhoc: {
        create: '➕ Thêm Môn Học Mới',
        edit: '✏️ Sửa Thông Tin Môn Học'
    },
    sinhvien: {
        create: '➕ Thêm Sinh Viên Mới',
        edit: '✏️ Sửa Thông Tin Sinh Viên'
    },
    ctdaotao: {
        create: '➕ Thêm Môn Vào CTĐT',
        edit: ''
    },
    dangky: {
        create: '➕ Đăng Ký Môn Học',
        edit: '✏️ Cập Nhật Điểm Thi'
    }
};

export const PRIMARY_KEYS = {
    khoa: 'MaKhoa',
    monhoc: 'MaMH',
    sinhvien: 'MaSV',
    ctdaotao: ['MaKhoa', 'KhoaHoc', 'MaMH'],
    dangky: ['MaSV', 'MaMon']
};
