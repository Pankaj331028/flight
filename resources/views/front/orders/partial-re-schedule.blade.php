<section class=" mb-5">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="table_wrapper">
                    <table class="table table-striped">
                        <thead class="thead-dark">
                          <tr>
                            <th class="rounded-left">Date</th>
                            <th class=" text-center">Status</th>
                            <th class="rounded-right text-center">Action</th>
                          </tr>
                        </thead>
                        <tbody>
                        @foreach($schedules as $schedule)
                            <tr>
                                <td><span class="dark_table_text">{{ $schedule->date }}</span></td>
                                <td class="text-center"><span class="colorTheme">{{ $schedule->status }}</span></td>
                                @if($schedule->status=='cancelled' || $schedule->status=='cancel initiated' || $schedule->status=='returned')<td></td>@endif
                                @if($product->scheduled==0)
                                    @if($schedule->status=='cancelled' || $schedule->status=='cancel initiated' || $schedule->status=='returned')
                                    @elseif($schedule->status=='delivered')
                                        <td class="text-center"><a href="{{ url('/chat-support') }}" class="reschedule danger_table_text">Return</a></td>
                                    @else
                                    <form id="cancelProduct" action="{{ url('api/cancelOrder') }}" method="post">
                                        <input type="hidden" name="item_id" value="{{ $product->id }}">
                                        <input type="hidden" name="user_id" value="{{ $auth_user->id }}">
                                        <input type="hidden" name="date" value="{{ $schedule['date'] }}">
                                        <input type="hidden" name="order_id" value="{{ $id }}">
                                        <td class="text-center"><button type="submit" class="" style="background: none;">Cancel</button></td>
                                    </form>
                                    @endif
                                @endif
                                @if($product->scheduled==1)
                                    @if($schedule->status=='pending')
                                        <td class="text-center"><a href="javascript:;" onclick="editReschedule(this)" data-date="{{ $schedule->date }}" data-item_id="{{ $product->id }}" data-id="{{$id}}" class="reschedule danger_table_text">Reschedule/Cancel</a></td>
                                    @endif
                                    @if($schedule->status=='rescheduled')
                                    <form id="cancelProduct" action="{{ url('api/cancelOrder') }}" method="post">
                                        <input type="hidden" name="item_id" value="{{ $product->id }}">
                                        <input type="hidden" name="user_id" value="{{ $auth_user->id }}">
                                        <input type="hidden" name="date" value="{{ $schedule['date'] }}">
                                        <input type="hidden" name="order_id" value="{{ $id }}">
                                        <td class="text-center"><button type="submit" class="" style="background: none;">Cancel</button></td>
                                    </form>
                                    @endif
                                @endif
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>