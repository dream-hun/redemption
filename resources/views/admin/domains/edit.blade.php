@extends('layouts.admin')
@section('page-title')
    Domains
@endsection
@section('breadcrumb')
    <ol class="breadcrumb float-sm-right">
        <li class="breadcrumb-item"><a href="{{ route('admin.domains.index')}}">Domains</a></li>
        <li class="breadcrumb-item active">Manage {{ $domain->name}}</li>
    </ol>
@endsection
@section('content')
    @if(session('status'))
        <div class="alert alert-success" role="alert">
            {{ session('status') }}
        </div>
    @endif
    <div class="row">
        <div class="col-md-12">
            <div class="card">
              <div class="card-header p-2">
                <ul class="nav nav-pills">
                  <li class="nav-item"><a class="nav-link active" href="#nameserver" data-toggle="tab">Nameserver Management</a></li>
                  <li class="nav-item"><a class="nav-link" href="#contacts" data-toggle="tab">Domain Contacts</a></li>
                  <li class="nav-item"><a class="nav-link" href="#renew" data-toggle="tab">Renew Domain</a></li>
                </ul>
              </div><!-- /.card-header -->
              <div class="card-body">
                    <div class="tab-content">
                        <div class="tab-pane active" id="nameserver">
                            <!-- Post -->
                            <div class="post">
                                @include('admin.domains.nameserver')
                            </div>
                        </div>
                        <!-- /.tab-pane -->
                        <div class="tab-pane" id="contacts">
                            <div class="row">

                                    @include('admin.domains.contact')


                            </div>
                        </div>

                  <!-- /.tab-pane -->

                  <div class="tab-pane" id="renew">
                    @include('admin.domains.renew')
                  </div>
                  <!-- /.tab-pane -->
                </div>
                <!-- /.tab-content -->
              </div><!-- /.card-body -->
            </div>
            <!-- /.card -->
          </div>
    </div>
@endsection

</div>
