@php
use App\Model\Category;
use App\Model\Attribute;
use App\Model\ProductVariation;
$category = Category::where('status', 'AC')->get();
$attribute = Attribute::where('status', 'AC')->get();

$min_price = ProductVariation::where('status', 'AC')->min('special_price');
$max_price = ProductVariation::where('status', 'AC')->max('special_price');

@endphp

<style>
input#hidden {
    display: none;
}
   </style>
<div class="col-lg-3 mb-4">
   <div class="fliter_box_inner">
      <div class="row">
         @if(request()->route()->id == 0)
         <div class="col-12 border-bottom py-3">
            <div class="filter_box box_css">
               <h2 class="mb-3"> Category </h2>
               <div class="checkbox_comman">
                  <form action="">
                     <div class="row">
                        @if(isset($category) && count($category) > 0)
                        @foreach($category as $key => $item)
                        <div class="col-12">
                           <div class="form-group custom_checkbox mb-2">
                              <input type="checkbox" name="categories[]" id="category_{{$item->id}}" class="filter category" value="{{$item->id}}">
                              <label for="category_{{$item->id}}">{{$item->name ?? ''}}</label>
                           </div>
                        </div>
                        @endforeach
                        @endif
                     </div>
                  </form>
               </div>
            </div>
         </div>
         @else
         <input type="checkbox" name="categories[]" class="filter category" value="{{request()->route()->id}}" id="hidden" checked>
         @endif

         <div class="col-12 border-bottom py-3">
            <div class="filter_box box_css">
               @if(isset($attribute) && count($attribute) > 0)
               @foreach($attribute as $key => $item)
               <h2 class="mb-3"> {{$item->name ?? ''}}  </h2>
               <div class="size_checkbox">
                  <div class="checkbox_comman">
                     <form action="">
                        <div class="row">
                           @foreach($item->options as $key => $d)
                           <div class="col-12">
                              <div class="form-group custom_checkbox mb-2">
                                 <input type="checkbox" name="attributes[{{$item->id}}][]" id="value_{{$d->id}}" data-value="{{$item->id}}" value="{{$d->id}}" class="filter attribute">
                                 <label for="value_{{$d->id}}"> {{$d->value ?? ''}}  </label>
                              </div>
                           </div>
                           @endforeach
                        </div>
                     </form>
                  </div>
               </div>
               @endforeach
               @endif
            </div>
         </div>

         <input type="hidden" class="min_price"  name="min_price" value="{{$min_price}}">
         <input type="hidden" class="max_price" name="max_price" value="{{$max_price}}">


         <div class="col-12 py-3">
            <div class="filter_box box_css">
               <h2 class="mb-3">Price -</h2>
               <div class="range-slider">
                  <span class="rangeValues"></span>
                  <input value="{{$min_price}}" min="{{$min_price}}" max="{{$max_price}}" step="10" type="range" class="filter">
                  <input value="{{$max_price}}" min="{{$min_price}}" max="{{$max_price}}" step="10" type="range" class="filter">
               </div>
            </div>
         </div>
      </div>
   </div>
</div>
@push('js')
<script>

$('document').ready(function() {

   $('#filter_dropdown_box li').click(function(){
      $('#filter_dropdown_box').attr('data-value',$(this).find('a').attr('data-value'));
      $($('.filter')[0]).change();

   })


    $('.filter').change(function() {
        $("#overlay").fadeIn(300);



        var categories_id = [];
        $('.category:checked').each(function(i){
          categories_id[i] = $(this).val();
        });


        cat_id = '';
        cat_id = $('#cat_ids').val();

        var attributesArray = [];
        $('.attribute:checked').each(function(i){
        var id =  $(this).attr("data-value");
           attributesArray.push($(this).val());
        });

        var min_price =  $('.min_price').val();
        var max_price =  $('.max_price').val();
        var order_by =  $('#filter_dropdown_box').attr('data-value');
        var type = "{{ $type ?? ''}}";

        $.ajax({
            url: "{{route('productFilter')}}",
            type: "get",
            dataType : 'html',
            data: {
               attributesArray:attributesArray,
               categories_id: categories_id,
               min_price:min_price,
               max_price:max_price,
               order_by:order_by,
               type:type,
            },
            success: function(result) {
               $('#productResult').empty();
               $('#productResult').html(result);
               $("#overlay").fadeOut(300);
            },
            error: function(error) {
                // var d = error.responseJSON;
                toastr.error('Something went wrong Please reload page');
            }
        });
    });
});
</script>

@endpush
