<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines contain the default error messages used by
    | the validator class. Some of these rules have multiple versions such
    | as the size rules. Feel free to tweak each of these messages here.
    |
    */

    'accepted'        => ':Attribute phải được chấp nhận.',
    'active_url'      => ':Attribute không phải là một URL hợp lệ.',
    'after'           => ':Attribute phải là một ngày sau :date.',
    'after_or_equal'  => ':Attribute phải là một ngày sau hoặc bằng :date.',
    'alpha'           => ':Attribute chỉ có thể chứa các chữ cái.',
    'alpha_dash'      => ':Attribute chỉ có thể chứa các chữ cái, số, dấu gạch ngang và dấu gạch dưới.',
    'alpha_num'       => ':Attribute chỉ có thể chứa các chữ cái và số.',
    'array'           => ':Attribute phải là một mảng.',
    'before'          => ':Attribute phải là một ngày trước :date.',
    'before_or_equal' => ':Attribute phải là một ngày trước hoặc bằng :date.',
    'between'         =>
    [
        'numeric' => ':Attribute phải có giữa :min và :max.',
        'file'    => ':Attribute phải có giữa :min và :max kilobytes.',
        'string'  => ':Attribute phải có giữa :min và :max ký tự.',
        'array'   => ':Attribute phải có giữa :min và :max mặt hàng.',
    ],
    'boolean'        => ':Attribute trường phải đúng hoặc sai.',
    'confirmed'      => ':Attribute nhận đinh không phù hợp.',
    'date'           => ':Attribute không phải là ngày hợp lệ.',
    'date_equals'    => ':Attribute phải là một ngày bằng :date.',
    'date_format'    => ':Attribute không phù hợp với định dạng :format.',
    'different'      => ':Attribute và :other phải khác.',
    'digits'         => ':Attribute cần phải :digits chữ số.',
    'digits_between' => ':Attribute phải nằm trong khoảng :min và :max.',
    'dimensions'     => ':Attribute có kích thước hình ảnh không hợp lệ.',
    'distinct'       => ':Attribute trường có giá trị trùng lặp.',
    'email'          => ':Attribute phải là một địa chỉ email hợp lệ.',
    'ends_with'      => ':Attribute phải kết thúc bằng một trong những điều sau :values.',
    'exists'         => 'Thuộc tính :Attribute đã chọn không hợp lệ.',
    'file'           => ':Attribute phải là một tập tin.',
    'filled'         => ':Attribute trường phải có một giá trị.',
    'gt'             =>
    [
        'numeric' => ':Attribute phải lớn hơn :value.',
        'file'    => ':Attribute phải lớn hơn :value kilobytes.',
        'string'  => ':Attribute phải lớn hơn :value ký tự.',
        'array'   => ':Attribute phải có nhiều hơn :value mặt hàng.',
    ],
    'gte' =>
    [
        'numeric' => ':Attribute phải lớn hơn hoặc bằng :value.',
        'file'    => ':Attribute phải lớn hơn hoặc bằng :value kilobytes.',
        'string'  => ':Attribute phải lớn hơn hoặc bằng :value ký tự.',
        'array'   => ':Attribute phải có :value mặt hàng hoặc hơn.',
    ],
    'image'    => ':Attribute phải là một hình ảnh.',
    'in'       => 'Thuộc tính :Attribute đã chọn không hợp lệ.',
    'in_array' => ':Attribute trường không tồn tại trong :other.',
    'integer'  => ':Attribute phải là số nguyên.',
    'ip'       => ':Attribute phải là một địa chỉ IP hợp lệ.',
    'ipv4'     => ':Attribute phải là địa chỉ IPv4 hợp lệ.',
    'ipv6'     => ':Attribute phải là địa chỉ IPv6 hợp lệ.',
    'json'     => ':Attribute phải là một chuỗi JSON hợp lệ.',
    'lt'       =>
    [
        'numeric' => ':Attribute phải nhỏ hơn :value.',
        'file'    => ':Attribute phải nhỏ hơn :value kilobytes.',
        'string'  => ':Attribute phải nhỏ hơn :value ký tự.',
        'array'   => ':Attribute phải có ít hơn :value mặt hàng.',
    ],
    'lte' =>
    [
        'numeric' => ':Attribute phải nhỏ hơn hoặc bằng :value.',
        'file'    => ':Attribute phải nhỏ hơn hoặc bằng :value kilobytes.',
        'string'  => ':Attribute phải nhỏ hơn hoặc bằng :value ký tự.',
        'array'   => ':Attribute không được có nhiều hơn :value mặt hàng.',
    ],
    'max' =>
    [
        'numeric' => ':Attribute có thể không lớn hơn :max.',
        'file'    => ':Attribute có thể không lớn hơn :max kilobytes.',
        'string'  => ':Attribute có thể không lớn hơn :max ký tự.',
        'array'   => ':Attribute có thể không có nhiều hơn :max mặt hàng.',
    ],
    'mimes'     => ':Attribute phải là một loại tệp: :values.',
    'mimetypes' => ':Attribute phải là một loại tệp: :values.',
    'min'       =>
    [
        'numeric' => ':Attribute ít nhất phải là :min.',
        'file'    => ':Attribute ít nhất phải là :min kilobytes.',
        'string'  => ':Attribute ít nhất phải là :min ký tự.',
        'array'   => ':Attribute phải có ít nhất :min mặt hàng.',
    ],
    'not_in'               => 'Thuộc tính :Attribute đã chọn không họp lệ.',
    'not_regex'            => ':Attribute định dạng không hợp lệ.',
    'numeric'              => ':Attribute phải là một số.',
    'password'             => 'Mật khẩu không đúng.',
    'present'              => ':Attribute trường phải có mặt.',
    'regex'                => ':Attribute định dạng không hợp lệ.',
    'required'             => ':Attribute là trường không được để trống.',
    'required_if'          => ':Attribute trường được yêu cầu khi :other is :value.',
    'required_unless'      => ':Attribute trường là bắt buộc trừ khi :other trong :values.',
    'required_with'        => ':Attribute trường được yêu cầu khi :values là món quà.',
    'required_with_all'    => ':Attribute trường được yêu cầu khi :values có mặt.',
    'required_without'     => ':Attribute trường được yêu cầu khi :values không phải bây giờ.',
    'required_without_all' => ':Attribute trường là bắt buộc khi không có :values có mặt.',
    'same' => ':Attribute và :other phải phù hợp với.',
    'size' =>
    [
        'numeric' => ':Attribute phải chứa :size.',
        'file'    => ':Attribute phải chứa :size kilobytes.',
        'string'  => ':Attribute phải chứa :size ký tự.',
        'array'   => ':Attribute phải chứa :size mặt hàng.',
    ],
    'starts_with' => ':Attribute phải bắt đầu bằng một trong những điều sau: :values.',
    'string'      => ':Attribute phải là một chuỗi.',
    'timezone'    => ':Attribute phải là một vùng hợp lệ.',
    'unique'      => ':Attribute đã được đăng ký.',
    'uploaded'    => ':Attribute không tải lên được.',
    'url'         => ':Attribute định dạng không hợp lệ.',
    'uuid'        => ':Attribute phải là một UUID hợp lệ.',

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | Here you may specify custom validation messages for attributes using the
    | convention "attribute.rule" to name the lines. This makes it quick to
    | specify a specific custom language line for a given attribute rule.
    |
    */

    'custom' =>
    [
        'attribute-name' =>
        [
            'rule-name' => 'custom-message',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Attributes
    |--------------------------------------------------------------------------
    |
    | The following language lines are used to swap our attribute placeholder
    | with something more reader friendly such as "E-Mail Address" instead
    | of "email". This simply helps us make our message more expressive.
    |
    */

    'attributes'           => [
        'address'               => 'địa chỉ',
        'age'                   => 'tuổi',
        'available'             => 'có sẵn',
        'body'                  => 'nội dung',
        'city'                  => 'thành phố',
        'content'               => 'nội dung',
        'country'               => 'quốc gia',
        'date'                  => 'ngày',
        'day'                   => 'ngày',
        'description'           => 'mô tả',
        'email'                 => 'email',
        'excerpt'               => 'trích dẫn',
        'first_name'            => 'tên',
        'gender'                => 'giới tính',
        'hour'                  => 'giờ',
        'last_name'             => 'họ',
        'message'               => 'lời nhắn',
        'minute'                => 'phút',
        'mobile'                => 'di động',
        'month'                 => 'tháng',
        'name'                  => 'tên',
        'password'              => 'mật khẩu',
        'password_confirmation' => 'xác nhận mật khẩu',
        'phone'                 => 'số điện thoại',
        'second'                => 'giây',
        'sex'                   => 'giới tính',
        'size'                  => 'kích thước',
        'subject'               => 'tiêu đề',
        'time'                  => 'thời gian',
        'title'                 => 'tiêu đề',
        'username'              => 'tên đăng nhập',
        'year'                  => 'năm',
        'full_name'              => 'họ và tên',
        'faculty'               => 'Khoa',
        'date_of_birth'         => 'Ngày sinh',
        'image'                 => 'Ảnh',
        "birthday"              => "Ngày sinh",
        'avatar'                => "Ảnh đại diện",
        'point'                 => "Điểm số",
        "student_id"            => "Sinh viên"
    ],



];
