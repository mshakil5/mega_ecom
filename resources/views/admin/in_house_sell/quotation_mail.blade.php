@extends('admin.layouts.admin')

@section('content')

<section class="content pt-3" id="contentContainer">
    <div class="container-fluid">
      <div class="row justify-content-md-center">
        <div class="col-md-8">
          <div class="mb-3">
            <a href="{{ route('allquotations') }}" class="btn btn-secondary">
              <i class="fa fa-arrow-left"></i> Back
            </a>
          </div>

          <div class="card card-secondary">
            <div class="card-header">
              <h3 class="card-title">Email</h3>
            </div>
            
            <form action="{{ route('orders.send-email', $order->id) }}" method="POST">
              @csrf
              <div class="card-body">

                <div class="row">
                    <div class="col-sm-12">
                      <div class="form-group">
                        <label>Subject</label>
                        <input type="text" class="form-control" id="subject" name="subject">
                      </div>
                    </div>
                    <div class="col-sm-12">
                      <div class="form-group">
                        <label>Body</label>
                        <textarea name="body" id="body" cols="30" rows="5" class="form-control">
                            <p><br></p><p><br></p><p><br></p><table border="0" cellspacing="0" cellpadding="0" width="642" style="color: rgb(34, 34, 34); width: 481.5pt;"><tbody><tr style="height: 62.15pt;"><td width="198" valign="top" style="margin: 0px; width: 148.85pt; border-width: medium 1pt medium medium; border-style: none solid none none; border-color: currentcolor rgb(68, 84, 106) currentcolor currentcolor; padding: 0cm 8.5pt; height: 62.15pt;"><p class="MsoNormal" align="center" style="margin-right: 0px; margin-bottom: 0px; margin-left: 0px; text-align: center;"><span style="font-size: 12pt; font-family: Calibri, sans-serif;"><u></u>&nbsp;<u></u></span></p><p class="MsoNormal" align="center" style="margin-right: 0px; margin-bottom: 0px; margin-left: 0px; text-align: center;"><img width="200" height="89" src="https://ci3.googleusercontent.com/mail-sig/AIorK4yOIslfYtS56bN6lPGFtdKWn34k_NNz5VKMXO--Jg5mvE4DW05t08nVJ9QqKJmMd7ec9Pteaqr-kxUH" class="CToWUd" data-bit="iit"><br></p></td><td width="444" valign="top" style="margin: 0px; width: 332.85pt; padding: 0cm 8.5pt; height: 62.15pt;"><p class="MsoNormal" style="margin-right: 0px; margin-bottom: 0px; margin-left: 0px;"><b><span style="font-size: 9pt; color: rgb(0, 32, 96);">Mob:</span></b><span style="font-size: 9pt; color: black;">&nbsp;07533498883<u></u><u></u></span></p><p class="MsoNormal" style="margin-right: 0px; margin-bottom: 0px; margin-left: 0px;"><b><span style="font-size: 9pt; color: rgb(0, 32, 96);">Tel:</span></b><span style="font-size: 9pt; color: rgb(0, 32, 96);">&nbsp;</span><span style="color: rgb(0, 0, 0); font-size: 12px;">07533498883</span></p><p class="MsoNormal" style="margin-right: 0px; margin-bottom: 0px; margin-left: 0px;"><b><span style="font-size: 9pt; color: rgb(0, 32, 96);">Email:</span></b><span style="font-size: 9pt; color: black;">&nbsp;</span><font color="#0563c1" face="Aptos Display, sans-serif"><span style="font-size: 13.3333px;"><a href="mailto:fozla.bhuyain@mentosoftware.co.uk" target="_blank" style="color: rgb(17, 85, 204);">fozla.bhuyain@<wbr>mentosoftware.co.uk</a></span></font></p><p class="MsoNormal" style="margin-right: 0px; margin-bottom: 0px; margin-left: 0px;"><b><span style="font-size: 9pt; color: rgb(0, 32, 96);">Web:</span></b><span style="font-size: 9pt;">&nbsp;</span><span style="font-size: 9pt;"><a href="http://www.mentosoftware.co.uk/" target="_blank" data-saferedirecturl="https://www.google.com/url?q=http://www.mentosoftware.co.uk&amp;source=gmail&amp;ust=1739628452205000&amp;usg=AOvVaw1QkY8V9UJApVRjki2lM505" style="color: rgb(17, 85, 204);"><span style="color: rgb(68, 114, 196);">www.mentosftware.co.uk</span></a></span><u><span style="font-size: 9pt; color: rgb(68, 114, 196);"><br></span></u><b><span style="font-size: 9pt;"><font color="#0563c1"><a href="https://g.page/r/CU8UuUq3_rGlEAE/review" target="_blank" data-saferedirecturl="https://www.google.com/url?q=https://g.page/r/CU8UuUq3_rGlEAE/review&amp;source=gmail&amp;ust=1739628452205000&amp;usg=AOvVaw05MPEWrom64NihWXktpaLB" style="color: rgb(17, 85, 204);">Google page</a></font><font color="#4472c4">&nbsp;</font><font color="#4472c4">|</font><span style="color: rgb(68, 114, 196);">&nbsp;</span><a href="https://www.linkedin.com/in/fozla-bhuyain-95090b34/" target="_blank" data-saferedirecturl="https://www.google.com/url?q=https://www.linkedin.com/in/fozla-bhuyain-95090b34/&amp;source=gmail&amp;ust=1739628452205000&amp;usg=AOvVaw0CpW-Skr_uIwNNS1GfrrBh" style="color: rgb(17, 85, 204);"><span style="color: rgb(5, 99, 193);">LinkedIn</span></a></span></b><span style="font-size: 9pt; color: black;"><u></u><u></u></span></p></td></tr><tr><td width="198" valign="top" style="margin: 0px; width: 148.85pt; border-width: medium 1pt medium medium; border-style: none solid none none; border-color: currentcolor rgb(68, 84, 106) currentcolor currentcolor; padding: 0cm 8.5pt;"><p class="MsoNormal" style="margin-right: 0px; margin-bottom: 0px; margin-left: 0px;"><br></p><p class="MsoNormal" align="center" style="margin-right: 0px; margin-bottom: 0px; margin-left: 0px; text-align: center;"><span style="font-size: 9pt; color: rgb(118, 113, 113);">Registered in England and Wales No.12989858</span></p></td><td width="444" valign="top" style="margin: 0px; width: 332.85pt; padding: 0cm 8.5pt;"><p class="MsoNormal" style="margin: 6pt 0px 0px; line-height: 14.95px;"><span style="font-size: 9pt; line-height: 13.8px; color: rgb(118, 113, 113);">&nbsp;24 Ince way, Kingsmead, Milton Keynes,&nbsp;MK44NP.<u></u><u></u></span></p><p class="MsoNormal" style="margin-right: 0px; margin-bottom: 0px; margin-left: 0px;"><br style="font-family: Arial, Helvetica, sans-serif; font-size: small;"></p></td></tr></tbody></table>
                        </textarea>
                      </div>
                    </div>
                </div>

              </div>

              <div class="card-footer">
                <button type="submit" class="btn btn-secondary" id="sendEmailButton">Send</button>
                <div id="loader" style="display: none;">
                    <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                    Loading...
                </div>
              </div>
            </form>

          </div>
        </div>
      </div>
    </div>
</section>

@endsection
@section('script')

<script>
  $(function() {
    $('#body').summernote({
        height: 300,
        toolbar: [
          ['style', ['style']],
          ['font', ['bold', 'italic', 'underline', 'clear']],
          ['fontname', ['fontname']],
          ['fontsize', ['fontsize']],
          ['color', ['color']],
          ['para', ['ul', 'ol', 'paragraph']],
          ['height', ['height']],
          ['insert', ['link', 'picture', 'video']],
          ['view', ['fullscreen', 'codeview', 'help']]
        ]
    });

  });
</script>

@endsection