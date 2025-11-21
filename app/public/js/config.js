/**
 * Configuration and constants for the application
 */

export const API_BASE = 'http://localhost:8080';

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
            label: 'Mã Khoa',
            type: 'text',
            required: true,
            maxlength: 10,
            placeholder: 'Ví dụ: CNTT, NN, LUAT'
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
            label: 'Mã Khoa',
            type: 'text',
            required: true,
            maxlength: 10
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
            label: 'Mã Môn Học',
            type: 'text',
            required: true,
            maxlength: 10
        }
    ],
    dangky: {
        create: [
            {
                name: 'MaSV',
                label: 'Mã Sinh Viên',
                type: 'text',
                required: true,
                maxlength: 20,
                placeholder: 'Ví dụ: 25DH000001, 24DH000002'
            },
            {
                name: 'MaMon',
                label: 'Mã Môn Học',
                type: 'text',
                required: true,
                maxlength: 10,
                placeholder: 'Ví dụ: MH001, MH002'
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
