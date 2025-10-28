<form><div class="row"><div class="mb-3 col-12 col-md-4 col-lg-3">
                                    <div class="form-group">
                                        <label for="course_id">Course Id</label><br><select name="course_id" class="form-select @error('course_id') is-invalid @enderror" id="view_course_id" disabled><option value="">Select Course Id</option></select>@error('course_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    </div>
<div class="mb-3 col-12 col-md-4 col-lg-3">
                                    <div class="form-group">
                                        <label for="title">Title</label><br><input type="text" name="title" class="form-control @error('title') is-invalid @enderror" value="{{ old('title', $query->title ?? "") }}" placeholder="Enter Title..." id="view_title" disabled>@error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    </div>
<div class="mb-3 col-12 col-md-4 col-lg-3">
                                    <div class="form-group">
                                        <label for="description">Description</label><br><textarea name="description" class="form-control @error('description') is-invalid @enderror" placeholder="Enter Description..." id="view_description" disabled>{{ old('description', $query->description ?? "") }}</textarea>@error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    </div>
<div class="mb-3 col-12 col-md-4 col-lg-3">
                                    <div class="form-group">
                                        <label for="order">Order</label><br><input type="number" name="order" min="0" class="form-control @error('order') is-invalid @enderror" value="{{ old('order', $query->order ?? "") }}" placeholder="Enter Order..." id="view_order" disabled>@error('order')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    </div>
<div class="mb-3 col-12 col-md-4 col-lg-3">
                                    <div class="form-group">
                                        <label for="is_published">Is Published</label><br><input type="radio" name="is_published" id="view_is_published_yes" value="1" {{ old('is_published', $query->is_published ?? "") == 1 ? "checked" : "" }} disabled> 
                                           <label for="editis_published_yes" disabled>Is Published Yes </label>
                                           <input type="radio" name="is_published" id="view_is_published_no" value="0" {{ old('is_published', $query->is_published ?? "") == 0 ? "checked" : "" }} disabled> 
                                           <label for="editis_published_no" disabled>Is Published No </label>@error('is_published')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    </div><div class="mb-3 text-right">
                              <a type="button" class="btn bg-danger" href="{{ route('admin.course-modules.index') }}">Close</a>
                          </div>
                      </div>
                  </form>