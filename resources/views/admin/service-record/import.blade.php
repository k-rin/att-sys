<!-- View stored in resources/views/admin/service-record/import.blade.php -->

@extends('admin.layouts.layout')
@section('content')
    <div class="container w-75 p-5 ms-1">
        <div id="uploadStatus"></div>
            <form id="uploadForm" enctype="multipart/form-data">
                @csrf
                <div class="input-group mb-3">
                    <input type="file" name="filename" class="form-control" id="inputFile" required>
                    <button type="submit" class="btn btn-outline-dark" id="uploadButton">Import</button>
                </div>
            </form>
        <div class="progress" id="progress"><div class="progress-bar bg-dark progress-bar-striped progress-bar-animated"></div></div>
    </div>
    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script>
        $(() => {
            $('#progress').hide();
            $('#uploadForm').on('submit', (e) => {
                e.preventDefault();
                const form = document.getElementById('uploadForm');
                const config = {
                    onUploadProgress: (e) => {
                        const percentCompleted = Math.round((e.loaded / e.total) * 100);
                        $('.progress-bar').width(percentCompleted + '%');
                        $('.progress-bar').html(percentCompleted + '%');
                    }
                };
                axios.interceptors.request.use((config) => {
                    $('#uploadButton').attr('disabled', true);
                    $('#progress').show();
                    $('.progress-bar').width('0%');
                    $('#uploadStatus').html('<div class="alert alert-info" role="alert">File uploading....</div>');
                    return config;
                });
                axios
                .post('/admin/service-record/import', form, config)
                .then(() => {
                    $('#uploadForm')[0].reset();
                    $('#uploadStatus').html('<div class="alert alert-success" role="alert">File has uploaded successfully!</div>');
                })
                .catch(() => {
                    $('#uploadStatus').html('<div class="alert alert-danger" role="alert">File upload failed, please try again.</div>');
                })
                .finally(() => {
                    $('#progress').hide();
                    $('#uploadButton').attr('disabled', false);
                });
            });
        });
    </script>
@endsection