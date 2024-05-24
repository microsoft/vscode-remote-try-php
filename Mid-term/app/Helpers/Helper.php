<?php
    function upLoadImage($data,$path): string
    {
        $imageName = time() . '.' . $data->getClientOriginalExtension();
        \Illuminate\Support\Facades\Storage::disk('local')->put('public/images/'.$path .'/'. $imageName, file_get_contents($data));
        return $imageName;
    }
